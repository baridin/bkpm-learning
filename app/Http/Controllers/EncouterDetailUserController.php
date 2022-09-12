<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataRestored;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use App\Http\Controllers\Voyager\Controller as VController;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\EncouterDetailUser;
use App\EncouterUser;

use App\Encouter;
use Illuminate\Database\Eloquent\Collection;
use Yajra\DataTables\DataTables;

use App\User;
use App\Remedial;
use App\Mail\EmailRemedial;
use App\Certificate;
use Illuminate\Database\Eloquent\Builder;
use App\Diklat;
use App\DiklatDetail;
use App\DiklatDetailUser;
use App\DiklatUser;
use App\DiklatBobotUser;
use App\DiklatBobot;

class EncouterDetailUserController extends VController
{
    use BreadRelationshipParser;

    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Browse our Data Type (B)READ
    //
    //****************************************

    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];
        $searchable = $dataType->server_side ? array_keys(SchemaManager::describeTable(app($dataType->model_name)->getTable())->toArray()) : '';
        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', null);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + 1;
            $orderColumn = [[$index, 'desc']];
            if (!$sortOrder && isset($dataType->order_direction)) {
                $sortOrder = $dataType->order_direction;
                $orderColumn = [[$index, $dataType->order_direction]];
            } else {
                $orderColumn = [[$index, 'desc']];
            }
        }

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } else {
                $query = $model::select('*');
            }

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses($model)) && app('VoyagerAuth')->user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        if (($isModelTranslatable = is_bread_translatable($model))) {
            $dataTypeContent->load('translations');
        }

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchable',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted'
        ));
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |__) |
    //               |  _  /
    //               | | \ \
    //               |_|  \_\
    //
    //  Read an item of our Data Type B(R)EAD
    //
    //****************************************

    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $isSoftDeleted = false;

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
            if ($dataTypeContent->deleted_at) {
                $isSoftDeleted = true;
            }
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        // Replace relationships' keys for labels and create READ links if a slug is provided.
        $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'read');

        // Check permission
        $this->authorize('read', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'isSoftDeleted'));
    }

    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        }

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        event(new BreadDataUpdated($dataType, $data));

        return redirect()
        ->route("voyager.{$dataType->slug}.index")
        ->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->display_name_singular}",
            'alert-type' => 'success',
        ]);
    }

    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0)
                            ? new $dataType->model_name()
                            : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));

        return redirect()
        ->route("voyager.{$dataType->slug}.index")
        ->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item BREA(D)
    //
    //****************************************

    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('delete', app($dataType->model_name));

        // Init array of IDs
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

            $model = app($dataType->model_name);
            if (!($model && in_array(SoftDeletes::class, class_uses($model)))) {
                $this->cleanup($dataType, $data);
            }
        }

        $displayName = count($ids) > 1 ? $dataType->display_name_plural : $dataType->display_name_singular;

        $res = $data->destroy($ids);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_deleted')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_deleting')." {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataDeleted($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    public function restore(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('delete', app($dataType->model_name));

        // Get record
        $model = call_user_func([$dataType->model_name, 'withTrashed']);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        $data = $model->findOrFail($id);

        $displayName = $dataType->display_name_singular;

        $res = $data->restore($id);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_restored')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_restoring')." {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataRestored($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    /**
     * Remove translations, images and files related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $dataType
     * @param \Illuminate\Database\Eloquent\Model $data
     *
     * @return void
     */
    protected function cleanup($dataType, $data)
    {
        // Delete Translations, if present
        if (is_bread_translatable($data)) {
            $data->deleteAttributeTranslations($data->getTranslatableAttributes());
        }

        // Delete Images
        $this->deleteBreadImages($data, $dataType->deleteRows->where('type', 'image'));

        // Delete Files
        foreach ($dataType->deleteRows->where('type', 'file') as $row) {
            if (isset($data->{$row->field})) {
                foreach (json_decode($data->{$row->field}) as $file) {
                    $this->deleteFileIfExists($file->download_link);
                }
            }
        }

        // Delete media-picker files
        $dataType->rows->where('type', 'media_picker')->where('details.delete_files', true)->each(function ($row) use ($data) {
            $content = $data->{$row->field};
            if (isset($content)) {
                if (!is_array($content)) {
                    $content = json_decode($content);
                }
                if (is_array($content)) {
                    foreach ($content as $file) {
                        $this->deleteFileIfExists($file);
                    }
                } else {
                    $this->deleteFileIfExists($content);
                }
            }
        });
    }

    /**
     * Delete all images related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $data
     * @param \Illuminate\Database\Eloquent\Model $rows
     *
     * @return void
     */
    public function deleteBreadImages($data, $rows)
    {
        foreach ($rows as $row) {
            if ($data->{$row->field} != config('voyager.user.default_avatar')) {
                $this->deleteFileIfExists($data->{$row->field});
            }

            if (isset($row->details->thumbnails)) {
                foreach ($row->details->thumbnails as $thumbnail) {
                    $ext = explode('.', $data->{$row->field});
                    $extension = '.'.$ext[count($ext) - 1];

                    $path = str_replace($extension, '', $data->{$row->field});

                    $thumb_name = $thumbnail->name;

                    $this->deleteFileIfExists($path.'-'.$thumb_name.$extension);
                }
            }
        }

        if ($rows->count() > 0) {
            event(new BreadImagesDeleted($data, $rows));
        }
    }

    /**
     * Order BREAD items.
     *
     * @param string $table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        if (!isset($dataType->order_column) || !isset($dataType->order_display_column)) {
            return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => __('voyager::bread.ordering_not_set'),
                'alert-type' => 'error',
            ]);
        }

        $model = app($dataType->model_name);
        if ($model && in_array(SoftDeletes::class, class_uses($model))) {
            $model = $model->withTrashed();
        }
        $results = $model->orderBy($dataType->order_column, $dataType->order_direction)->get();

        $display_column = $dataType->order_display_column;

        $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->whereField($display_column)->first();

        $view = 'voyager::bread.order';

        if (view()->exists("voyager::$slug.order")) {
            $view = "voyager::$slug.order";
        }

        return Voyager::view($view, compact(
            'dataType',
            'display_column',
            'dataRow',
            'results'
        ));
    }

    public function update_order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        $model = app($dataType->model_name);

        $order = json_decode($request->input('order'));
        $column = $dataType->order_column;
        foreach ($order as $key => $item) {
            if ($model && in_array(SoftDeletes::class, class_uses($model))) {
                $i = $model->withTrashed()->findOrFail($item->id);
            } else {
                $i = $model->findOrFail($item->id);
            }
            $i->$column = ($key + 1);
            $i->save();
        }
    }

    public function action(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $action = new $request->action($dataType, null);

        return $action->massAction(explode(',', $request->ids), $request->headers->get('referer'));
    }

    /**
     * Get BREAD relations data.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function relation(Request $request)
    {
        $slug = $this->getSlug($request);
        $page = $request->input('page');
        $on_page = 50;
        $search = $request->input('search', false);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $rows = $request->input('method', 'add') == 'add' ? $dataType->addRows : $dataType->editRows;
        foreach ($rows as $key => $row) {
            if ($row->field === $request->input('type')) {
                $options = $row->details;
                $skip = $on_page * ($page - 1);

                // If search query, use LIKE to filter results depending on field label
                if ($search) {
                    $total_count = app($options->model)->where($options->label, 'LIKE', '%'.$search.'%')->count();
                    $relationshipOptions = app($options->model)->take($on_page)->skip($skip)
                        ->where($options->label, 'LIKE', '%'.$search.'%')
                        ->get();
                } else {
                    $total_count = app($options->model)->count();
                    $relationshipOptions = app($options->model)->take($on_page)->skip($skip)->get();
                }

                $results = [];
                foreach ($relationshipOptions as $relationshipOption) {
                    $results[] = [
                        'id'   => $relationshipOption->{$options->key},
                        'text' => $relationshipOption->{$options->label},
                    ];
                }

                return response()->json([
                    'results'    => $results,
                    'pagination' => [
                        'more' => ($total_count > ($skip + $on_page)),
                    ],
                ]);
            }
        }

        // No result found, return empty array
        return response()->json([], 404);
    }

//     public function getReadyAssesment(Request $request)
//     {
//         $users = new Collection();
//         $diklat = Diklat::all();
//         $diklatDetail = DiklatDetail::all();
//         $mataDiklat = DB::table('diklat_mata_diklats')->select('diklat_mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id', 'diklat_mata_diklats.diklat_id', 'mata_diklats.title')
//         ->leftJoin('mata_diklats', 'mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id')
//         ->get();
//         $ujian = Encouter::all();

//         $data = Encouter::whereHas('details.users', function(Builder $query){
//             $query->where('value', null);
//         })->select('id', 'title')->orderByDesc('id')->get();
//         if ($data->count() > 0) {
//             foreach ($data as $kn => $en) {
//                 $detailEssay = $en->details->where('type', 'essay')->first();
//                 foreach ($detailEssay->users as $ks => $vs) {
//                 $nilai = EncouterDetailUser::whereEncouterDetailId($detailEssay->id)->whereUserId($vs->id)->first();
                
// if (!is_null($nilai) && empty($nilai->value)) {
//                         $users->push([
//                             'id' => $en->id,
//                             'user_id' => $vs->id,
//                             'ujian' => $en->title,
//                             'users' => $vs->name,
//                         ]);
//                     }
//                 }
                
                
//             }
//         }
//         $type = 'udah';

//         return view('vendor.voyager.encouter-detail-users.ready-assesment', compact('users', 'diklat', 'diklatDetail', 'mataDiklat', 'ujian', 'type'));
//     }

    public function getReadyAssesment(Request $request)
    {
        $users = new Collection();
        $diklat = Diklat::all();
        $diklatDetail = DiklatDetail::all();
        $mataDiklat = DB::table('diklat_mata_diklats')->select('diklat_mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id', 'diklat_mata_diklats.diklat_id', 'mata_diklats.title')
        ->leftJoin('mata_diklats', 'mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id')
        ->get();
        $ujian = Encouter::all();

        $data = Encouter::whereHas('details.users', function(Builder $query){
            $query->where('value', null);
        })->select('id', 'title')->orderByDesc('id')->get();
        if ($data->count() > 0) {
            foreach ($data as $kn => $en) {
                $detailEssay = $en->details->where('type', 'essay')->first();
                foreach ($detailEssay->users as $ks => $vs) {
                $nilai = EncouterDetailUser::whereEncouterDetailId($detailEssay->id)->whereUserId($vs->id)->where('value',null)->first();
                
if ($nilai) {
                        $users->push([
                            'id' => $en->id,
                            'user_id' => $vs->id,
                            'ujian' => $en->title,
                            'users' => $vs->name,
                        ]);
                    }
                }
                
                
            }
        }
        $type = 'udah';

        return view('vendor.voyager.encouter-detail-users.ready-assesment', compact('users', 'diklat', 'diklatDetail', 'mataDiklat', 'ujian', 'type'));
    }

    public function getRemedial(Request $request)
    {

        // $datas = $request->all();

        // $users = new Collection();
        // $users_mantap = new Collection();
        // $semua = DiklatDetailUser::where('diklat_id', $datas['diklat'])
        // ->where('diklat_detail_id', $datas['angkatan'])
        // ->get();
        // $ujian = Encouter::where('id', $datas['ujian'])->first();
        // $data = Encouter::whereHas('details.users', function(Builder $query){
        //     $query->where('value', null);
        // })->select('id', 'title')->orderByDesc('id')->where('id', $datas['ujian'])->get();
        // // if ($data->count() > 0) {
        //     foreach ($data as $kn => $en) {
        //         $detailEssay = $en->details->where('type', 'essay')->first();
        //         foreach ($detailEssay->users as $ks => $vs) {
        //         $nilai = EncouterDetailUser::whereEncouterDetailId($detailEssay->id)->whereUserId($vs->id)->first();
        //         if (!is_null($nilai) && empty($nilai->value)) {
        //                 $users->push([
        //                     'id' => $en->id,
        //                     'user_id' => $vs->id,
        //                     'ujian' => $en->title,
        //                     'users' => $vs->name,
        //                 ]);
        //             }
        //         }
        //     // }
        // }

        // foreach($semua as $sm)
        // {
        //     if($users->count() > 0)
        //     {
        //         foreach($users as $us)
        //         {
        //             if($us['user_id'] !== $sm['user_id'])
        //             {
        //                $users_mantap->push([
        //                 'id' => $datas['ujian'],
        //                 'user_id' => $sm['user_id'],
        //                 'ujian' => $us['ujian'],
        //                 'users' => $sm->users->name,
        //             ]);

        //             }
        //             else
        //             {
                       
        //             }
        //         }
        //     }
        //     else
        //     {
        //         $cek = EncouterDetailUser::where('user_id',$sm['user_id'])->first();
        //         if($cek){
        //             if($cek->nilai < 60){


        //             $users_mantap->push([
        //             'id' => $datas['ujian'],
        //             'user_id' => $sm['user_id'],
        //             'ujian' => $ujian['title'],
        //             'users' => $sm->users->name,
        //         ]);    
        //             }
        //         }
                
        //     }
        // }
        // $diklat = Diklat::all();
        // $diklatDetail = DiklatDetail::all();
        // $mataDiklat = DB::table('diklat_mata_diklats')->select('diklat_mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id', 'diklat_mata_diklats.diklat_id', 'mata_diklats.title')
        // ->leftJoin('mata_diklats', 'mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id')
        // ->get();
        // $ujian = Encouter::all();
        // $users = $users_mantap;
        // $type = 'belum';

        // return view('vendor.voyager.encouter-detail-users.remedial', compact('users', 'diklat', 'diklatDetail', 'mataDiklat', 'ujian', 'type', 'datas'));

        // print_r($request->all());

        $datas = $request->all();
        $get_diklat =  Diklat::where('id',$datas['diklat'])->first();
        $diklat_form = $datas['diklat'];
        $users = new Collection();
        $diklat = Diklat::all();
        $angkatan_form = $datas['angkatan'];
        $diklatDetail = DiklatDetail::all();
        $mataDiklat = DB::table('diklat_mata_diklats')->select('diklat_mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id', 'diklat_mata_diklats.diklat_id', 'mata_diklats.title')
        ->leftJoin('mata_diklats', 'mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id')
        ->get();
        $ujian = Encouter::all();
        $mata = DB::table('diklat_mata_diklats')->where('diklat_id',$datas['diklat'])->first();
        $mata_diklat = DB::table('mata_diklats')->where('id',$mata->mata_diklat_id)->first();
        $mata_diklat_form  = $datas['diklat'];
        $data = Encouter::whereHas('details.users')->where('id',$request->ujian)->get();
        $ujian_form = $request->ujian;
        if ($data->count() > 0) {
            foreach ($data as $kn => $en) {
                $detailEssay = $en->details->where('type', 'essay')->first();
                foreach ($detailEssay->users as $ks => $vs) {
                $nilai = EncouterDetailUser::whereEncouterDetailId($detailEssay->id)->whereUserId($vs->id)->first();
                // if ($get_diklat->getScore($vs->id) < 60) {
                       $cek_remedial =  EncouterUser::where('encouter_id',$request->ujian)->where('user_id',$vs->id)->first();
                        if($cek_remedial->assesment < 60 ){
                            $users->push([
                            'id' => $en->id,
                            'user_id' => $vs->id,
                            'ujian' => $en->title,
                            'users' => $vs->name,
                            'nilai' => $cek_remedial->assesment,
                        ]);
                        }
                            
                       
                        
                    // }
                }
            }
        }
        $type = 'belum';

        return view('vendor.voyager.encouter-detail-users.remedial', compact('users', 'diklat', 'diklatDetail', 'mataDiklat', 'ujian', 'type','datas','diklat_form','angkatan_form','mata_diklat_form','ujian_form'));


    }


    // public function getNotReadyAssesment(Request $request)
    // {
    //     $datas = $request->all();
    //     $users = new Collection();
    //     $diklat = Diklat::all();
    //     $diklatDetail = DiklatDetail::all();
    //     $mataDiklat = DB::table('diklat_mata_diklats')->select('diklat_mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id', 'diklat_mata_diklats.diklat_id', 'mata_diklats.title')
    //     ->leftJoin('mata_diklats', 'mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id')
    //     ->get();
    //     $ujian = Encouter::all();

    //     $data = Encouter::whereHas('details.users', function(Builder $query){
    //         $query->where('value', null);
    //     })->select('id', 'title')->orderByDesc('id')->get();
    //     if ($data->count() > 0) {
    //         foreach ($data as $kn => $en) {
    //             $detailEssay = $en->details->where('type', 'essay')->first();
    //             foreach ($detailEssay->users as $ks => $vs) {
    //             $nilai = EncouterDetailUser::whereEncouterDetailId($detailEssay->id)->whereUserId($vs->id)->first();
    //             if ($nilai->status_ujian == null) {
    //                     $users->push([
    //                         'id' => $en->id,
    //                         'nilai'=>$nilai->value,
    //                         'user_id' => $vs->id,
    //                         'ujian' => $en->title,
    //                         'users' => $vs->name,
    //                     ]);
    //                 }
    //             }
    //         }
    //     }
    //     $type = 'belum';

     

    //     return view('vendor.voyager.encouter-detail-users.not-assesment', compact('users', 'diklat', 'diklatDetail', 'mataDiklat', 'ujian', 'type', 'datas'));
    // }
   public function getNotReadyAssesment(Request $request)
    {
        $datas = $request->all();

        $users = new Collection();
        $users_mantap = new Collection();
        $semua = DiklatDetailUser::where('diklat_id', $datas['diklat'])
        ->where('diklat_detail_id', $datas['angkatan'])
        ->get();
        $ujian = Encouter::where('id', $datas['ujian'])->first();
        $data = Encouter::whereHas('details.users', function(Builder $query){
            $query->where('value', null);
        })->select('id', 'title')->orderByDesc('id')->where('id', $datas['ujian'])->get();
        if ($data->count() > 0) {
            foreach ($data as $kn => $en) {
                $detailEssay = $en->details->where('type', 'essay')->first();
                foreach ($detailEssay->users as $ks => $vs) {
                $nilai = EncouterDetailUser::whereEncouterDetailId($detailEssay->id)->whereUserId($vs->id)->first();
                if (!is_null($nilai) && empty($nilai->value)) {
                        $users->push([
                            'id' => $en->id,
                            'user_id' => $vs->id,
                            'ujian' => $en->title,
                            'users' => $vs->name,
                        ]);
                    }
                }
            }
        }

        foreach($semua as $sm)
        {
            if($users->count() > 0)
            {
                foreach($users as $us)
                {
                    if($us['user_id'] !== $sm['user_id'])
                    {
                    $get_user = User::where('id',$sm['user_id'])->first();
                    if(!empty($get_user->name)){
                        $nama = $get_user->name;
                    }else{
                        $nama = 'abdul';

                    }
                       $users_mantap->push([
                        'id' => $datas['ujian'],
                        'user_id' => $sm['user_id'],
                        'ujian' => $us['ujian'],
                        'users' => $nama,
                    ]);

                    }
                    else
                    {
                       
                    }
                }
            }
            else
            {
                $cek = EncouterDetailUser::where('user_id',$sm['user_id'])->where('encouter_id',$datas['ujian'])->first();

                $get_user = User::where('id',$sm['user_id'])->first();
                    if(!empty($get_user->name)){
                        $nama = $get_user->name;
                    }else{
                        $nama = 'abdul';

                    }
                    if(empty($cek)){
                        $cek_user_sudah = Certificate::where('diklat_id',$datas['diklat'])->where('diklat_detail_id',$datas['angkatan'])->where('user_id',$sm['user_id'])->first();
                        if($cek_user_sudah){

                        }else{
                            $users_mantap->push([
                                'id' => $datas['ujian'],
                                'user_id' => $sm['user_id'],
                                'ujian' => $ujian['title'],
                                'users' => $nama, 
                            ]);       
                        }


                    }

                
            }
        }


        $diklat = Diklat::all();
        $diklatDetail = DiklatDetail::all();
        $mataDiklat = DB::table('diklat_mata_diklats')->select('diklat_mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id', 'diklat_mata_diklats.diklat_id', 'mata_diklats.title')
        ->leftJoin('mata_diklats', 'mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id')
        ->get();
        $ujian = Encouter::all();
        $users = $users_mantap;
        $type = 'belum';

        return view('vendor.voyager.encouter-detail-users.not-assesment', compact('users', 'diklat', 'diklatDetail', 'mataDiklat', 'ujian', 'type', 'datas'));
    }


    public function sendEncounter(Request $request)
    {
        $datas = $request->all();

        $add = DB::table('encouters')
        ->where('id', $datas['ujian'])
        ->update([
            'start_at' => new Carbon($datas['start_at']),
            'duration' => $datas['durasi']
        ]);
        
        $users = new Collection();
        $users_mantap = new Collection();
        $semua = DiklatDetailUser::where('diklat_id', $datas['diklat'])
        ->where('diklat_detail_id', $datas['angkatan'])
        ->get();
        $ujian = Encouter::where('id', $datas['ujian'])->first();
         $ujiany = Encouter::where('id', $datas['ujian'])->first();
        $data = Encouter::whereHas('details.users', function(Builder $query){
            $query->where('value', null);
        })->select('id', 'title')->orderByDesc('id')->where('id', $datas['ujian'])->get();
        if ($data->count() > 0) {
            foreach ($data as $kn => $en) {
                $detailEssay = $en->details->where('type', 'essay')->first();
                foreach ($detailEssay->users as $ks => $vs) {
                $nilai = EncouterDetailUser::whereEncouterDetailId($detailEssay->id)->whereUserId($vs->id)->first();
                if (!is_null($nilai) && empty($nilai->value)) {
                        $users->push([
                            'id' => $en->id,
                            'user_id' => $vs->id,
                            'ujian' => $en->title,
                            'users' => $vs->name,
                        ]);
                    }
                }
            }
        }

        foreach($semua as $sm)
        {

            if($users->count() > 0)
            {
                foreach($users as $us)
                {
                    $get_user = User::where('id',$sm['user_id'])->first();
                    if(!empty($get_user->name)){
                        $nama = $get_user->name;
                        $email = $get_user->email;
                    }else{
                        $nama = 'abdul';
                        $email = 'marlita1979@yahoo.com';

                    }

                        $users_mantap->push([
                            'id' => $datas['ujian'],
                            'user_id' => $sm['user_id'],
                            'ujian' => $us['ujian'],
                            'users' => $nama,
                            'email' => $email,
                        ]);
                    
                }
            }
            else
            {
                $cek = EncouterDetailUser::where('user_id',$sm['user_id'])->first();
                $get_user = User::where('id',$sm['user_id'])->first();
                    if(!empty($get_user->name)){
                        $nama = $get_user->name;
                        $email = $get_user->email;
                    }else{
                        $nama = 'abdul';
                        $email = 'marlita1979@yahoo.com';

                    }

                if(empty($cek)){
                    $cek_user_sudah = Certificate::where('diklat_id',$datas['diklat'])->where('diklat_detail_id',$datas['angkatan'])->where('user_id',$sm['user_id'])->first();
                    if($cek_user_sudah){

                    }else{
                        $users_mantap->push([
                    'id' => $datas['ujian'],
                    'user_id' => $sm['user_id'],
                    'ujian' => $ujian['title'],
                    'users' => $nama, 
                     'email' => $email,
                    ]);       
                    }
                    
                
                }
            }
        }

        $diklat = Diklat::all();
        $diklatDetail = DiklatDetail::all();
        $mataDiklat = DB::table('diklat_mata_diklats')->select('diklat_mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id', 'diklat_mata_diklats.diklat_id', 'mata_diklats.title')
        ->leftJoin('mata_diklats', 'mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id')
        ->get();
        $ujian = Encouter::all();
        $users = $users_mantap;
        
        foreach($users as $r)
        {
            Mail::to($r['email'])->send(new \App\Mail\EmailForQueuing($request->root().'/my-course/'.$datas['diklat'].'/'.$ujiany->mata_diklat_id.'/ujian/40/'.$datas['ujian']));
        }
        
        return redirect()->back();
    }
    public function sendRemedial(Request $request)
    {
          $datas = $request->all();
        $add = DB::table('encouters')
        ->where('id', $datas['ujian'])
        ->update([
            'start_at' => new Carbon($datas['start_at']),
            'duration' => $datas['durasi']
        ]);
        $get_diklat =  Diklat::where('id',$datas['diklat'])->first();
       
        $users = new Collection();
        $diklat = Diklat::all();
        $diklatDetail = DiklatDetail::all();
        $mataDiklat = DB::table('diklat_mata_diklats')->select('diklat_mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id', 'diklat_mata_diklats.diklat_id', 'mata_diklats.title')
        ->leftJoin('mata_diklats', 'mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id')
        ->get();
        $ujian = Encouter::all();
          $ujiany = Encouter::where('id', $datas['ujian'])->first();
        $data = Encouter::whereHas('details.users')->where('id',$request->ujian)->get();
        if ($data->count() > 0) {
            foreach ($data as $kn => $en) {
                $detailEssay = $en->details->where('type', 'essay')->first();
                foreach ($detailEssay->users as $ks => $vs) {
                $nilai = EncouterDetailUser::whereEncouterDetailId($detailEssay->id)->whereUserId($vs->id)->first();

                // if ($get_diklat->getScore($vs->id)  < 60) {
                      $cek_remedial =  EncouterUser::where('encouter_id',$request->ujian)->where('user_id',$vs->id)->first();
                        if($cek_remedial->assesment < 60 ){
                            $users->push([
                            'id' => $en->id,
                            'user_id' => $vs->id,
                            'ujian' => $en->title,
                            'users' => $vs->name,
                            'nilai' => $cek_remedial->assesment,
                        ]);
                             EncouterDetailUser::where('user_id',$vs->id)->where('encouter_id',$datas['ujian'])->delete();
                        EncouterUser::where('user_id',$vs->id)->where('encouter_id',$datas['ujian'])->update(['status_ujian'=>'0','assesment'=>'0']);

                        
                         
                    Mail::to($vs->email)->send(new EmailRemedial($request->root().'/my-course/'.$datas['diklat'].'/'.$ujiany->mata_diklat_id.'/ujian/40/'.$datas['ujian']));
                    
                                   
                           
                        }
                        
                        
                       
                    
                    // }

                }

            }
        }else{
        return "tidak ada ";     
        }
        $type = 'belum';
        
         
        return redirect()->back();
    }

    public function encounterAssesment(Request $request)
    {
        $datas = new Collection();
        $use = User::findOrFail($request->get('usr_id'));
        $encounter = Encouter::with('details.users')->findOrFail($request->get('enc_id'));
        $nilai = EncouterUser::whereEncouterId($request->get('enc_id'))->whereUserId($request->get('usr_id'))->get();
        foreach ($encounter->details as $ke => $ve) {
            $is = $ve->users($use->id)->first();
            if (!empty($is)) {
                if ($ve->type == 'essay') {
                    $data = [
                        'detail_id' => $ve->id,
                        'user_id' => $use->id,
                        'soal' => $ve->value,
                        'nilai' => (empty($is->value))?0:$is->value,
                        'answer' => $is->answer,
                        'type' => 'essay',
                    ];
                } else {
                    $data = [
                        'detail_id' => $ve->id,
                        'user_id' => $use->id,
                        'soal' => ($ve->key == 'soal')?$ve->value:null,
                        'nilai' => (empty($is->value))?0:$is->value,
                        'answer' => $is->answer,
                        'type' => 'pg',
                    ];
                }
                $datas->push($data);
            }
        }
        return view('vendor.voyager.encouter-detail-users.encounter-assesment', compact('use', 'datas', 'nilai', 'encounter'));
    }

    // public function postAssesment(Request $request)
    // {
       
    //     $user = User::findOrFail($request->user_id);
    //     $enc = Encouter::findOrFail($request->encounter_id);
    //     $nilai = [];
    //     foreach ($enc->details as $ke => $ve) {
    //         if ($request->has("value_{$ve->id}")) {
    //             $ve->users($user->id)->first()->update([
    //                 'value' => $request->post("value_{$ve->id}")
    //             ]);
    //             array_push($nilai, $request->post("value_{$ve->id}"));
    //         }
    //     }
    //     $t = count($nilai) * 100;
    //     $nilai_sum = (array_sum(array_values($nilai))/$t)*100;
    //     $enus = EncouterUser::updateOrCreate(
    //         ['encouter_id' => $enc->id, 'user_id' => $user->id],
    //         ['assesment' => round($nilai_sum)]
    //     );

    //     $reqUser = $request->user_id;
        
    //     $encounter = Encouter::with('details.users')->findOrFail($enc->id);
    //     $diklat = Diklat::with('users')->find($encounter->diklat_id);

    //     $userCertificate = Certificate::where('user_id', $user->id)
    //         ->where('diklat_id', $encounter->diklat_id)
    //         ->where('diklat_detail_id', $encounter->diklat_detail_id)
    //         ->first();
    //         $total_nilai = $diklat->getScore($request->user_id);  
    //         $getNilai = $diklat->getNilai($request->user_id);
    //         if($getNilai < 60){
    //             EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['is_remedial'=>1]);
    //             Remedial::create(['user_id'=>$user->id,'diklat_id'=>$encounter->diklat_id,'encouter_id'=>$encounter->id,'nilai'=>$getNilai]);

                
    //             DiklatDetailUser::where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->where('diklat_detail_id',$encounter->diklat_detail_id)->update(['status_nilai'=>'1']);

    //         }else{

    //              $total_nilai = $diklat->getScore($request->user_id); 
    //             if($total_nilai < 60){
    //                  EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['is_remedial'=>1]);
    //             Remedial::create(['user_id'=>$user->id,'diklat_id'=>$encounter->diklat_id,'encouter_id'=>$encounter->id,'nilai'=>$getNilai]);

                
    //             DiklatDetailUser::where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->where('diklat_detail_id',$encounter->diklat_detail_id)->update(['status_nilai'=>'1']);
    //             }else{

    //             DiklatDetailUser::where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->where('diklat_detail_id',$encounter->diklat_detail_id)->update(['status_nilai'=>'1']);
    //             if($enus->is_remedial == 1){
    //                 $nilai = 60;
    //                 $is_remedial = 1;
    //             }else{
    //                 $nilai = $diklat->getScore($request->user_id);    
    //                 $is_remedial = 0;
    //             }

    //             $cek_status_ujian = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->where('status_ujian','0')->first();
    //             if($cek_status_ujian){

    //             }else{
    //                 if (empty($userCertificate)) {

    //                     $userDiklat = $diklat->users()->pluck('users.id')->toArray();
    //                     $noAbsen = array_search($reqUser, $userDiklat);
    //                     $absensi = str_pad(round($noAbsen + 1), 3, '0', STR_PAD_LEFT);
    //                     Certificate::create([
    //                         'user_id' => $user->id,
    //                         'diklat_id' => $encounter->diklat_id,
    //                         'diklat_detail_id' => $encounter->diklat_detail_id,
    //                         'no_absen' => $absensi,
    //                         'no_certificate' => 'empty',
    //                         'nilai' => $nilai,
    //                         'is_remedial'=>$is_remedial,
    //                     ]);
    //                 }else{

    //                     $userDiklat = $diklat->users()->pluck('users.id')->toArray();
    //                     $noAbsen = array_search($reqUser, $userDiklat);
    //                     $absensi = str_pad(round($noAbsen + 1), 3, '0', STR_PAD_LEFT);
    //                     Certificate::where('diklat_detail_id',$encounter->diklat_detail_id)->where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->update([
    //                         'user_id' => $user->id,
    //                         'diklat_id' => $encounter->diklat_id,
    //                         'diklat_detail_id' => $encounter->diklat_detail_id,
    //                         'no_absen' => $absensi,
    //                         'no_certificate' => 'empty',
    //                         'nilai' => $nilai,
    //                         'is_remedial'=>$is_remedial,
    //                     ]);

    //                 }   
    //             }
                    
    //             }



                
    //         }
        
    //     return redirect('admin/get-user-assesment')->with([
    //         'message'    => __('voyager::generic.successfully_updated')." Nilai ujian",
    //         'alert-type' => 'success',
    //     ]);
    // }
    public function postAssesment(Request $request)
    {

        $user = User::findOrFail($request->user_id);
        $enc = Encouter::findOrFail($request->encounter_id);
        $nilai = [];
        foreach ($enc->details as $ke => $ve) {
            if ($request->has("value_{$ve->id}")) {
                $ve->users($user->id)->first()->update([
                    'value' => $request->post("value_{$ve->id}")
                ]);
                array_push($nilai, $request->post("value_{$ve->id}"));
            }
        }
        $t = count($nilai) * 100;
        $nilai_sum = (array_sum(array_values($nilai))/$t)*100;
        $enus = EncouterUser::updateOrCreate(
            ['encouter_id' => $enc->id, 'user_id' => $user->id],
            ['assesment' => round($nilai_sum)]
        );

        $reqUser = $request->user_id;

        $encounter = Encouter::with('details.users')->findOrFail($enc->id);
        $diklat = Diklat::with('users')->find($encounter->diklat_id);

        $userCertificate = Certificate::where('user_id', $user->id)
        ->where('diklat_id', $encounter->diklat_id)
        ->where('diklat_detail_id', $encounter->diklat_detail_id)
        ->first();
        $total_nilai = $diklat->getScore($request->user_id);  

        if($nilai_sum < 60){

            EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['is_remedial'=>1,'assesment'=>round($nilai_sum),'status_ujian'=>'1']);
// 

            DiklatDetailUser::where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->where('diklat_detail_id',$encounter->diklat_detail_id)->update(['status_nilai'=>'1']);
        }else{
            $total_nilai = $diklat->getScore($request->user_id); 
            $cek_nilai_akhir_ujian3 = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->first();
            if($cek_nilai_akhir_ujian3->is_remedial == '1'){
                EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['assesment'=>60,'status_ujian'=>'1']);
            }else{
                EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['assesment'=>round($nilai_sum),'status_ujian'=>'1']);    
            }

            if($total_nilai < 60){

            }else{

                DiklatDetailUser::where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->where('diklat_detail_id',$encounter->diklat_detail_id)->update(['status_nilai'=>'1']);
                if($enus->is_remedial == 1){
                    $nilai = 60;
                    $is_remedial = 1;
                }else{
                    $nilai = $diklat->getScore($request->user_id);    
                    $is_remedial = 0;
                }

                $cek_status_ujian = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->where('status_ujian','0')->first();
                if($cek_status_ujian){
                    $cek_nilai_akhir_ujian = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->first();
                    if($cek_nilai_akhir_ujian->is_remedial == '1'){
                        EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['assesment'=>60,'status_ujian'=>'1']);
                    }else{
                        EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['assesment'=>round($nilai_sum),'status_ujian'=>'1']);    
                    }

                }else{

                    if (empty($userCertificate)) {
                        $cek_nilai_sertif = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->first();
                        if($cek_nilai_sertif->is_remedial == '1'){
                            $nilai_sertif = 60;
                            $remedialll = 1;

                        }else{
                            $nilai_sertif = $diklat->getScore($request->user_id);
                            $remedialll = 0;
                        }
                        $sum = 0; $nomor=1;
                        $diklatss = Diklat::findOrFail($enc->diklat_id);
                        foreach ($diklatss->mataDiklat as $mataDiklat) {
                            $encouteree = Encouter::where('mata_diklat_id',$mataDiklat->id)->where('diklat_id',$enc->diklat_id)->first();
                            $dkkkd = EncouterUser::where('user_id',$user->id)->where('encouter_id',$encouteree->id)->first();

                            $sum+=       $dkkkd->assesment;
                            $jumlah_uye = $sum / $nomor++;


                        }

                        $userDiklat = $diklat->users()->pluck('users.id')->toArray();
                        $noAbsen = array_search($reqUser, $userDiklat);
                        $absensi = str_pad(round($noAbsen + 1), 3, '0', STR_PAD_LEFT);
                        Certificate::create([
                            'user_id' => $user->id,
                            'diklat_id' => $encounter->diklat_id,
                            'diklat_detail_id' => $encounter->diklat_detail_id,
                            'no_absen' => $absensi,
                            'no_certificate' => 'empty',
                            'nilai' => $jumlah_uye,
                            'is_remedial'=>$remedialll,
                        ]);
                    }else{
                        $cek_nilai_sertif = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->first();
                        if($cek_nilai_sertif->is_remedial == '1'){
                            $nilai_sertif = 60;
                            $remedialll = 1;

                        }else{
                            $nilai_sertif = $diklat->getScore($request->user_id);
                            $remedialll = 0;
                        }
                        $sum = 0; $nomor=1;
                        $diklatss = Diklat::findOrFail($enc->diklat_id);
                        foreach ($diklatss->mataDiklat as $mataDiklat) {
                            $encouteree = Encouter::where('mata_diklat_id',$mataDiklat->id)->where('diklat_id',$enc->diklat_id)->first();
                            $cek_remedialss = EncouterUser::where('user_id',$user->id)->where('encouter_id',$encouteree->id)->first();


                            $sum+=       $cek_remedialss->assesment;
                            $jumlah_uyesss = $sum / $nomor++;


                        }

                        $userDiklat = $diklat->users()->pluck('users.id')->toArray();
                        $noAbsen = array_search($reqUser, $userDiklat);
                        $absensi = str_pad(round($noAbsen + 1), 3, '0', STR_PAD_LEFT);
                        Certificate::where('diklat_detail_id',$encounter->diklat_detail_id)->where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->update([
                            'user_id' => $user->id,
                            'diklat_id' => $encounter->diklat_id,
                            'diklat_detail_id' => $encounter->diklat_detail_id,
                            'no_absen' => $absensi,
                            'no_certificate' => 'empty',
                            'nilai' => $jumlah_uyesss,
                            'is_remedial'=>$remedialll,
                        ]);

                    }

                }


            }

        }

        return redirect('admin/get-user-assesment')->with([
            'message'    => __('voyager::generic.successfully_updated')." Nilai ujian",
            'alert-type' => 'success',
        ]);
    }
    public function backuppostAssesment(Request $request)
    {
        // dd($request->all());
        $user = User::findOrFail($request->user_id);
        $enc = Encouter::findOrFail($request->encounter_id);
        $nilai = [];
        foreach ($enc->details as $ke => $ve) {
            if ($request->has("value_{$ve->id}")) {
                $ve->users($user->id)->first()->update([
                    'value' => $request->post("value_{$ve->id}")
                ]);
                array_push($nilai, $request->post("value_{$ve->id}"));
            }
        }
        $t = count($nilai) * 100;
        $nilai_sum = (array_sum(array_values($nilai))/$t)*100;
        $enus = EncouterUser::updateOrCreate(
            ['encouter_id' => $enc->id, 'user_id' => $user->id],
            ['assesment' => round($nilai_sum)]
        );

        $reqUser = $request->user_id;
        
        $encounter = Encouter::with('details.users')->findOrFail($enc->id);
        $diklat = Diklat::with('users')->find($encounter->diklat_id);

        $userCertificate = Certificate::where('user_id', $user->id)
            ->where('diklat_id', $encounter->diklat_id)
            ->where('diklat_detail_id', $encounter->diklat_detail_id)
            ->first();
            $total_nilai = $diklat->getScore($request->user_id);  
            
            if($nilai_sum < 60){

                EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['is_remedial'=>1,'assesment'=>round($nilai_sum),'status_ujian'=>'1']);
                
                
                DiklatDetailUser::where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->where('diklat_detail_id',$encounter->diklat_detail_id)->update(['status_nilai'=>'1']);
            }else{
                 $total_nilai = $diklat->getScore($request->user_id); 
                $cek_nilai_akhir_ujian3 = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->first();
                    if($cek_nilai_akhir_ujian3->is_remedial == '1'){
                        EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['assesment'=>60,'status_ujian'=>'1']);
                    }else{
                        EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['assesment'=>round($nilai_sum),'status_ujian'=>'1']);    
                    }
                    
                if($total_nilai < 60){

                }else{

                    DiklatDetailUser::where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->where('diklat_detail_id',$encounter->diklat_detail_id)->update(['status_nilai'=>'1']);
                if($enus->is_remedial == 1){
                    $nilai = 60;
                    $is_remedial = 1;
                }else{
                    $nilai = $diklat->getScore($request->user_id);    
                    $is_remedial = 0;
                }
                
                $cek_status_ujian = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->where('status_ujian','0')->first();
                if($cek_status_ujian){
                    $cek_nilai_akhir_ujian = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->first();
                    if($cek_nilai_akhir_ujian->is_remedial == '1'){
                        EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['assesment'=>60,'status_ujian'=>'1']);
                    }else{
                        EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->update(['assesment'=>round($nilai_sum),'status_ujian'=>'1']);    
                    }
                    
                }else{

                    if (empty($userCertificate)) {
                    $cek_nilai_sertif = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->first();
                    if($cek_nilai_sertif->is_remedial == '1'){
                        $nilai_sertif = 60;
                          $remedialll = 1;

                    }else{
                        $nilai_sertif = $diklat->getScore($request->user_id);
                          $remedialll = 0;
                    }
                     $sum = 0; $nomor=1;
                     $diklatss = Diklat::findOrFail($enc->diklat_id);
                    foreach ($diklatss->mataDiklat as $mataDiklat) {
                      $encouteree = Encouter::where('mata_diklat_id',$mataDiklat->id)->where('diklat_id',$enc->diklat_id)->first();
                      $dkkkd = EncouterUser::where('user_id',$user->id)->where('encouter_id',$encouteree->id)->first();
                         $sum+=       $dkkkd->assesment;
                        $jumlah_uye = $sum / $nomor++;
                    }
                    

                    
                    $userDiklat = $diklat->users()->pluck('users.id')->toArray();
                    $noAbsen = array_search($reqUser, $userDiklat);
                    $absensi = str_pad(round($noAbsen + 1), 3, '0', STR_PAD_LEFT);
                    Certificate::create([
                        'user_id' => $user->id,
                        'diklat_id' => $encounter->diklat_id,
                        'diklat_detail_id' => $encounter->diklat_detail_id,
                        'no_absen' => $absensi,
                        'no_certificate' => 'empty',
                        'nilai' => $jumlah_uye,
                        'is_remedial'=>$remedialll,
                    ]);
                }else{
                    $cek_nilai_sertif = EncouterUser::where('encouter_id',$enc->id)->where('user_id',$user->id)->first();
                    if($cek_nilai_sertif->is_remedial == '1'){
                        $nilai_sertif = 60;
                        $remedialll = 1;

                    }else{
                        $nilai_sertif = $diklat->getScore($request->user_id);
                        $remedialll = 0;
                    }
                     $sum = 0; $nomor=1;
                    $diklatss = Diklat::findOrFail($enc->diklat_id);
                    foreach ($diklatss->mataDiklat as $mataDiklat) {
                      $encouteree = Encouter::where('mata_diklat_id',$mataDiklat->id)->where('diklat_id',$enc->diklat_id)->first();
                      $cek_remedialss = EncouterUser::where('user_id',$user->id)->where('encouter_id',$encouteree->id)->first();
                         $sum+=       $cek_remedialss->assesment;
                        $jumlah_uyesss = $sum / $nomor++;
                    }
                   
                    $userDiklat = $diklat->users()->pluck('users.id')->toArray();
                    $noAbsen = array_search($reqUser, $userDiklat);
                    $absensi = str_pad(round($noAbsen + 1), 3, '0', STR_PAD_LEFT);
                    Certificate::where('diklat_detail_id',$encounter->diklat_detail_id)->where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->update([
                        'user_id' => $user->id,
                        'diklat_id' => $encounter->diklat_id,
                        'diklat_detail_id' => $encounter->diklat_detail_id,
                        'no_absen' => $absensi,
                        'no_certificate' => 'empty',
                        'nilai' => $jumlah_uyesss,
                        'is_remedial'=>$remedialll,
                    ]);

                }
                    
                }

                   
                }
                
            }
        
        return redirect('admin/get-user-assesment')->with([
            'message'    => __('voyager::generic.successfully_updated')." Nilai ujian",
            'alert-type' => 'success',
        ]);
    }

    // Penilaian Bobot
    public function getReadyBobotAssesment(Request $request)
    {
        if (app('VoyagerAuth')->user()->name != 'Instruktur') {
            $users = new Collection();
            $title = 'Diklat';
            $data = DiklatUser::whereProgress(100)->orderByDesc('created_at')->get();
            if ($data->count() > 0) {
                foreach ($data as $kn => $en) {
                    $nilai = DiklatBobotUser::whereDiklatId($en->diklat_id)->whereUserId($en->user_id)->first();
                    $user = User::find($en->user_id);
                    $diklat = Diklat::find($en->diklat_id);
                    if (empty($nilai) && !empty($user) && !empty($diklat)) {
                        $users->push([
                            'id' => $en->diklat_id,
                            'user_id' => $en->user_id,
                            'ujian' => $diklat->title,
                            'users' => $user->name,
                        ]);
                    }
                }
            }
            return view('vendor.voyager.encouter-detail-users.ready-assesment', compact('users', 'title'));
        } else {
            abort(403);
        }
    }
    public function deleteAssesment(Request $request)
    {
        $reqUser = $request->get('usr_id');
        $user = User::findOrFail($reqUser);
        $encounter = Encouter::with('details.users')->findOrFail($request->get('enc_id'));
        $diklat = Diklat::with('users')->find($encounter->diklat_id);
        // $nilai = EncouterUser::whereEncouterId($request->get('enc_id'))->whereUserId($request->get('usr_id'))->get();
        foreach ($encounter->details as $ve) {
            $is = $ve->users($user->id)->first();
            if (!empty($is)) {
                EncouterDetailUser::where('encouter_detail_id', $ve->id)
                ->where('user_id', $user->id)
                ->where('answer', $is->answer)
                ->where('status', 0)
                ->update([
                    'status' => 1,
                ]);
            }
        }

        // Create certificate and transkip nilai
        $userCertificate = Certificate::where('user_id', $user->id)
            ->where('diklat_id', $encounter->diklat_id)
            ->where('diklat_detail_id', $encounter->diklat_detail_id)
            ->first();
        if (empty($userCertificate)) {
            $nilai = $diklat->getNilai($reqUser);
            $userDiklat = $diklat->users()->pluck('users.id')->toArray();
            $noAbsen = array_search($reqUser, $userDiklat);
            $absensi = str_pad(round($noAbsen + 1), 3, '0', STR_PAD_LEFT);
            Certificate::create([
                'user_id' => $user->id,
                'diklat_id' => $encounter->diklat_id,
                'diklat_detail_id' => $encounter->diklat_detail_id,
                'no_absen' => $absensi,
                'no_certificate' => 'empty',
                'nilai' => $nilai,
            ]);
        }else{
            $nilai = $diklat->getNilai($reqUser);
            $userDiklat = $diklat->users()->pluck('users.id')->toArray();
            $noAbsen = array_search($reqUser, $userDiklat);
            $absensi = str_pad(round($noAbsen + 1), 3, '0', STR_PAD_LEFT);
            Certificate::where('diklat_detail_id',$encounter->diklat_detail_id)->where('user_id',$user->id)->where('diklat_id',$encounter->diklat_id)->update([
                'user_id' => $user->id,
                'diklat_id' => $encounter->diklat_id,
                'diklat_detail_id' => $encounter->diklat_detail_id,
                'no_absen' => $absensi,
                'no_certificate' => 'empty',
                'nilai' => $nilai,
            ]);            
        }

        return redirect()->back()->with([
            'message'    => "Review berhasil dihapus",
            'alert-type' => 'success',
        ]);

    }
    public function review(Request $request)
    {
        $req = $request->all();
        $users = new Collection();
        $diklat = Diklat::all();
        $diklatDetail = DiklatDetail::all();
        $mataDiklat = DB::table('diklat_mata_diklats')->select('diklat_mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id', 'diklat_mata_diklats.diklat_id', 'mata_diklats.title')
        ->leftJoin('mata_diklats', 'mata_diklats.id', 'diklat_mata_diklats.mata_diklat_id')
        ->get();
        $ujian = Encouter::all();
        $data = new Encouter;

        if(isset($req['diklat']))
        {
            $data = $data->where('diklat_id', $req['diklat']);
        }

        if(isset($req['angkatan']))
        {
            $data = $data->where('diklat_detail_id', $req['angkatan']);
        }

        if(isset($req['mata-diklat']))
        {
            $data = $data->where('mata_diklat_id', $req['mata-diklat']);
        }

        $data = $data->whereHas('details.users', function(Builder $query){
            $query->where('encouter_detail_users.status', 0);
            $query->where('value', '>=', 0);
        })->select('id', 'title')->orderByDesc('id')->get();

        if ($data->count() > 0) {
            foreach ($data as $kn => $en) {
                $detailEssay = $en->details->where('type', 'essay')->first();
                foreach ($detailEssay->users as $ks => $vs) {
                $nilai = EncouterDetailUser::whereEncouterDetailId($detailEssay->id)->whereUserId($vs->id)->first();

                if (!is_null($nilai)) {
                        $users->push([
                            'id' => $en->id,
                            'user_id' => $vs->id,
                            'ujian' => $en->title,
                            'users' => $vs->name,
                            'user_id' => $vs->id,
                            'ujian_id' => $en->id,
                        ]);
                    }
                }
            }
        }
        $type = 'udah';

        return view('vendor.voyager.encouter-detail-users.review-assesment', compact('users', 'diklat', 'diklatDetail', 'mataDiklat', 'ujian', 'type'));
    }


    public function encounterBobotAssesment(Request $request)
    {
        $datas = new Collection();
        $use = User::findOrFail($request->get('usr_id'));
        $diklat = Diklat::findOrFail($request->get('enc_id'));
        $encounter = DiklatBobot::whereDiklatId($diklat->id)->get();
        foreach ($encounter as $ke => $ve) {
            // $diklat_b = DiklatBobotUser::find($ve->id);
            $data = [
                'detail_id' => $ve->id,
                'user_id' => $use->id,
                'soal' => $ve->title,
                'answer' => $ve->type,
            ];
            if ($ve->type == 'online') {
                $data['nilai'] = (empty($diklat->getScore($use->id)))?0:$diklat->getScore($use->id);
            } else {
                $data['nilai'] = 0;
            }

            $datas->push($data);
        }
        return view('vendor.voyager.encouter-detail-users.encounter-bobot-assesment', compact('use', 'datas', 'encounter', 'diklat'));
    }

    public function postBobotAssesment(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $diklat = Diklat::findOrFail($request->diklat_id);
        $enc = DiklatBobot::whereDiklatId($diklat->id)->get();
        $nilai = [];
        foreach ($enc as $ke => $ve) {
            if ($request->has("value_{$ve->id}")) {
                DiklatBobotUser::updateOrCreate(
                    ['diklat_bobot_id' => $ve->id, 'user_id' => $user->id],
                    ['diklat_id' => $diklat->id, 'assesment' => $request->post("value_{$ve->id}")]
                );
                array_push($nilai, $request->post("value_{$ve->id}"));
            }
        }
        return redirect('admin/get-user-bobot-assesment')->with([
            'message'    => __('voyager::generic.successfully_updated')." Nilai ujian",
            'alert-type' => 'success',
        ]);
    }
}
