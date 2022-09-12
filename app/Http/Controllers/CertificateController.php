<?php

namespace App\Http\Controllers;

use App\Certificate;
use App\CertificateSetting;
use App\Diklat;
use App\DiklatDetail;
use App\DiklatBobot;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
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
use App\Imports\NoCertificateImport;
use App\User;
use App\Nosertif;
use PDF;
use Illuminate\Support\Facades\Mail;
// use Barryvdh\DomPDF\PDF
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;

class CertificateController extends VController
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
        // $this->authorize('browse', app($dataType->model_name));

        // Get all diklat
        $diklats = Diklat::orderByDesc('created_at')->get();
        $diklatDetails = [];
        $selectedDiklatId = 0;
        $selectedAngkatanId = 0;
        foreach ($diklats as $diklat) {
            $mapDiklat = (object)[
                'id' => $diklat->id,
                'angkatan' => []
            ];
            $diklatDetail = DiklatDetail::whereDiklatId($diklat->id)->orderBy('created_at')->get();
            if ($diklatDetail->count() > 0) {
                foreach ($diklatDetail as $angkatan) {
                    $filterData = [
                        'id' => $angkatan->id,
                        'text' => $angkatan->title
                    ];
                    if ($request->get('diklatId') && $request->get('angkatanId')) {
                        $selectedDiklatId = (int)$request->get('diklatId');
                        $selectedAngkatanId = (int)$request->get('angkatanId');
                        $filterData['selected'] = $selectedDiklatId === $diklat->id && $selectedAngkatanId === $angkatan->id;
                    }
                    array_push($mapDiklat->angkatan, (object)$filterData);
                }
            }
            array_push($diklatDetails, $mapDiklat);
        }
        $diklatDetails = json_encode($diklatDetails);

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

            if ($selectedDiklatId && $selectedAngkatanId) {
                $query->whereDiklatId($selectedDiklatId)->whereDiklatDetailId($selectedAngkatanId);
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
            'diklats',
            'diklatDetails',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchable',
            'selectedDiklatId',
            'selectedAngkatanId',
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
        $nilai = 0;
        if ($request->source === 'manual' && count($request->details['nilai']) > 0) {
            $nilais = array_map('intval', $request->details['nilai']);
            $nilai = round(array_sum($nilais) / count($nilais));
        }
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
        $content = $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        if ($content->source === 'manual' && $nilai > 0) {
            $content->update([
                'nilai' => $nilai
            ]);
        }

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
         $user = User::find($request->user_id);
        $mes = "sapspaps";
        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));
        $sendMail = Mail::send([], ['name', 'Admin Pendaftaran Diklat E-Learning BKPM'], function ($message) use ($user, $mes) {
                if ($this->request->hasFile('attachment')) {
                    $publicPath = Storage::putFile('public/mail/konfirm-email', $this->request->file('attachment'));
                    $path = Storage::url($publicPath);
                    $message->attach(public_path().$path);
                }
                $message->to($user->email)
                    ->subject('Permintaan Pendaftaran Anda Telah Di Setujui.')
                    ->setBody((string)view('vendor.mail.custom-mail')->withMes($mes)->withUser($user), 'text/html');
            });
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

    public function generateCertificate(Certificate $certificate)
    {
        $user = User::find($certificate->user_id);
        $user = User::find($certificate->user_id);
        $file = $certificate->id.'.pdf';
        return  Redirect::url('digital-signatures/',$file);

        // if ($user->avatar == 'users/default.png') {
        //     session()->flash('warning_msg', "Anda harus mengganti foto profil anda terlebih dahulu.");
        //     return back();
        // } else {
        //     try {
        //         if (!file_exists(storage_path('app/public/' . $user->avatar))) {
        //             return dd("File photo user tidak ada, silahkan update terlebih dahulu photo user");
        //         }
        //         $diklat = Diklat::find($certificate->diklat_id);
        //         $detail = DiklatDetail::find($certificate->diklat_detail_id);
        //         $absensi = $certificate->absen;
        //         $certificateSetting = CertificateSetting::first();
        //         $nilai = 'Telah Mengikuti';
        //         $status = 'LULUS';
        //         $kualifikasi = '';
        //         if ($diklat->category_id == 1) {
        //             if ((int) $certificate->nilai >= 92.5) {
        //                 $nilai = 'Sangat Memuaskan';
        //                 $kualifikasi = $status . ' dengan kualifikasi ' . strtoupper($nilai);
        //             } elseif ((int) $certificate->nilai <= 92.5 && (int) $certificate->nilai >= 85) {
        //                 $nilai = 'Memuaskan';
        //                 $kualifikasi = $status . ' dengan kualifikasi ' . strtoupper($nilai);
        //             } elseif ((int) $certificate->nilai <= 85 && (int) $certificate->nilai >= 77.5) {
        //                 $nilai = 'Sangat Baik';
        //                 $kualifikasi = $status . ' dengan kualifikasi ' . strtoupper($nilai);
        //             } elseif ((int) $certificate->nilai <= 77.5 && (int) $certificate->nilai >= 70) {
        //                 $nilai = 'Baik';
        //                 $kualifikasi = $status . ' dengan kualifikasi ' . strtoupper($nilai);
        //             } elseif ((int) $certificate->nilai <= 70 && (int) $certificate->nilai >= 60) {
        //                 $nilai = 'Cukup';
        //                 $kualifikasi = $status . ' dengan kualifikasi ' . strtoupper($nilai);
        //             } elseif ((int) $certificate->nilai < 60) {
        //                 $nilai = 'Telah Mengikuti';
        //                 $kualifikasi = strtoupper($nilai);
        //             }
        //         } else {
        //             $kualifikasi = strtoupper($nilai);
        //         }
        //         $data = [
        //             'user' => $user,
        //             'diklat' => $diklat,
        //             'nilai' => $kualifikasi,
        //             'detail' => $detail,
        //             'absensi' => $absensi,
        //             'certificate' => $certificate,
        //             'certificateSetting' => $certificateSetting
        //         ];
        //         $dompdf PDF::loadView('frontend.sertificate.index', $data);
        //         /* (Optional) Setup the paper size and orientation */
        //         $dompdf->setPaper('a4', 'landscape');
        //         /* Output the generated PDF to Browser */
        //         return $dompdf->stream();
        //         // return view('frontend.sertificate.test', $data);
        //     } catch (\Throwable $th) {
        //         dd($th);
        //     }
        // }
    }
    

    // public function generateTranskipNilai(Certificate $certificate)
    // {
    //     try {
    //         $certificateSetting = CertificateSetting::first();
    //         try {
    //             $user = User::find($certificate->user_id);
    //         } catch (\Throwable $th) {
    //             echo 'User tidak di temukan';
    //         }
    //         try {
    //             $diklat = Diklat::find($certificate->diklat_id);
    //         } catch (\Throwable $th) {
    //             echo 'Diklat tidak di temukan';
    //         }
    //         try {
    //             $detail = DiklatDetail::find($certificate->diklat_detail_id);
    //         } catch (\Throwable $th) {
    //             echo 'Angkatan tidak di temukan';
    //         }
    //         $diklat = Diklat::find($certificate->diklat_id);
    //             $detail = DiklatDetail::find($certificate->diklat_detail_id);
    //             $absensi = $certificate->absen;
    //             $certificateSetting = CertificateSetting::first();
    //             $nilai = 'Telah Mengikuti';
    //             $status = 'LULUS';
    //             $kualifikasi = '';
    //             if ($diklat->category_id == 1) {
    //                 if ((int) $certificate->nilai >= 92.5) {
    //                     $nilai = 'Sangat Memuaskan';
    //                     $kualifikasi = strtoupper($nilai);
    //                 } elseif ((int) $certificate->nilai <= 92.5 && (int) $certificate->nilai >= 85) {
    //                     $nilai = 'Memuaskan';
    //                     $kualifikasi = strtoupper($nilai);
    //                 } elseif ((int) $certificate->nilai <= 85 && (int) $certificate->nilai >= 77.5) {
    //                     $nilai = 'Sangat Baik';
    //                     $kualifikasi = strtoupper($nilai);
    //                 } elseif ((int) $certificate->nilai <= 77.5 && (int) $certificate->nilai >= 70) {
    //                     $nilai = 'Baik';
    //                     $kualifikasi = strtoupper($nilai);
    //                 } elseif ((int) $certificate->nilai <= 70 && (int) $certificate->nilai >= 60) {
    //                     $nilai = 'Cukup';
    //                     $kualifikasi = strtoupper($nilai);
    //                 } elseif ((int) $certificate->nilai < 60) {
    //                     $nilai = 'Telah Mengikuti';
    //                     $kualifikasi = strtoupper($nilai);
    //                 }
    //             } else {
    //                 $kualifikasi = strtoupper($nilai);
    //             }

    //         if($certificate->is_remedial == 1){
    //                 $nilai_fix =  'Cukup';
                    
    //             }else{
    //                 $nilai_fix = $kualifikasi;
    //             }

    //             $data = [
    //                 'user' => $user,
    //                 'diklat' => $diklat,
    //                 'nilai' => $nilai_fix,
    //                 'detail' => $detail,
    //                 'absensi' => $absensi,
    //                 'certificate' => $certificate,
    //                 'certificateSetting' => $certificateSetting
    //             ];
    //         $dompdf = PDF::loadView('frontend.sertificate.transkip', $data);
    //         /* (Optional) Setup the paper size and orientation */
    //         $dompdf->setPaper('a4');
    //         /* Output the generated PDF to Browser */
    //         return $dompdf->stream();

    //     } catch (\Throwable $th) {
    //         throw $th;
    //     }
    // }
    public function generateTranskipNilai(Certificate $certificate)
    {
        try {
            $certificateSetting = CertificateSetting::first();
            // try {
            //     $user = User::find($certificate->user_id);
            // } catch (\Throwable $th) {
            //     echo 'User tidak di temukan';
            // }
            // try {
            //     $diklat = Diklat::find($certificate->diklat_id);
            // } catch (\Throwable $th) {
            //     echo 'Diklat tidak di temukan';
            // }
            // try {
            //     $detail = DiklatDetail::find($certificate->diklat_detail_id);
            // } catch (\Throwable $th) {
            //     echo 'Angkatan tidak di temukan';
            // }
            $diklat = Diklat::find($certificate->diklat_id);
                $detail = DiklatDetail::find($certificate->diklat_detail_id);
                $absensi = $certificate->absen;
                $certificateSetting = CertificateSetting::first();
                $nilai = 'Telah Mengikuti';
                $status = 'LULUS';
                $kualifikasi = '';
                if ($diklat->category_id == 1) {
                    if ((int) $certificate->nilai >= 92.5) {
                        $nilai = 'Sangat Memuaskan';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 92.5 && (int) $certificate->nilai >= 85) {
                        $nilai = 'Memuaskan';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 85 && (int) $certificate->nilai >= 77.5) {
                        $nilai = 'Sangat Baik';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 77.5 && (int) $certificate->nilai >= 70) {
                        $nilai = 'Baik';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 70 && (int) $certificate->nilai >= 60) {
                        $nilai = 'Cukup';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai < 60) {
                        $nilai = 'Telah Mengikuti';
                        $kualifikasi = strtoupper($nilai);
                    }
                } else {
                    $kualifikasi = strtoupper($nilai);
                }

            if($certificate->is_remedial == 1){
                    $nilai_fix =  'Cukup';
                    
                }else{
                    $nilai_fix = $kualifikasi;
                }

                $data = [
                    'user' => $user,
                    'diklat' => $diklat,
                    'nilai' => $nilai,
                    'detail' => $detail,
                    'absensi' => $absensi,
                    'certificate' => $certificate,
                    'certificateSetting' => $certificateSetting
                ];
            $dompdf = PDF::loadView('frontend.sertificate.transkip', $data);
            /* (Optional) Setup the paper size and orientation */
            $dompdf->setPaper('a4');
            /* Output the generated PDF to Browser */
            // return $dompdf->stream();
            $file = $certificate->id.'.pdf';
            return  Redirect::url('digital-signatures-transkip/',$file);


        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function generateTranskipNilaiCek(string $no)
    {
        $idCer = Crypt::decryptString($no);
            $certificate = Certificate::findOrFail($idCer);
        try {
            $certificateSetting = CertificateSetting::first();
            try {
                $user = User::find($certificate->user_id);
            } catch (\Throwable $th) {
                echo 'User tidak di temukan';
            }
            try {
                $diklat = Diklat::find($certificate->diklat_id);
            } catch (\Throwable $th) {
                echo 'Diklat tidak di temukan';
            }
            try {
                $detail = DiklatDetail::find($certificate->diklat_detail_id);
            } catch (\Throwable $th) {
                echo 'Angkatan tidak di temukan';
            }
            $diklat = Diklat::find($certificate->diklat_id);
                $detail = DiklatDetail::find($certificate->diklat_detail_id);
                $absensi = $certificate->absen;
                $certificateSetting = CertificateSetting::first();
                $nilai = 'Telah Mengikuti';
                $status = 'LULUS';
                $kualifikasi = '';
                if ($diklat->category_id == 1) {
                    if ((int) $certificate->nilai >= 92.5) {
                        $nilai = 'Sangat Memuaskan';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 92.5 && (int) $certificate->nilai >= 85) {
                        $nilai = 'Memuaskan';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 85 && (int) $certificate->nilai >= 77.5) {
                        $nilai = 'Sangat Baik';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 77.5 && (int) $certificate->nilai >= 70) {
                        $nilai = 'Baik';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 70 && (int) $certificate->nilai >= 60) {
                        $nilai = 'Cukup';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai < 60) {
                        $nilai = 'Telah Mengikuti';
                        $kualifikasi = strtoupper($nilai);
                    }
                } else {
                    $kualifikasi = strtoupper($nilai);
                }

            if($certificate->is_remedial == 1){
                    $nilai_fix =  'Cukup';
                    
                }else{
                    $nilai_fix = $kualifikasi;
                }

                $data = [
                    'user' => $user,
                    'diklat' => $diklat,
                    'nilai' => $nilai,
                    'detail' => $detail,
                    'absensi' => $absensi,
                    'certificate' => $certificate,
                    'certificateSetting' => $certificateSetting
                ];
            $dompdf = PDF::loadView('frontend.sertificate.transkip', $data);
            /* (Optional) Setup the paper size and orientation */
            $dompdf->setPaper('a4');
            /* Output the generated PDF to Browser */
            // return $dompdf->stream();
              
            /* (Optional) Setup the paper size and orientation */
            
            /* Output the generated PDF to Browser */
            return $dompdf->stream();


        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function generateTranskipNilaiAdmin(Certificate $certificate)
    {
        try {
            $certificateSetting = CertificateSetting::first();
            try {
                $user = User::find($certificate->user_id);
            } catch (\Throwable $th) {
                echo 'User tidak di temukan';
            }
            try {
                $diklat = Diklat::find($certificate->diklat_id);
            } catch (\Throwable $th) {
                echo 'Diklat tidak di temukan';
            }
            try {
                $detail = DiklatDetail::find($certificate->diklat_detail_id);
            } catch (\Throwable $th) {
                echo 'Angkatan tidak di temukan';
            }
            $diklat = Diklat::find($certificate->diklat_id);
                $detail = DiklatDetail::find($certificate->diklat_detail_id);
                $absensi = $certificate->absen;
                $certificateSetting = CertificateSetting::first();
                $nilai = 'Telah Mengikuti';
                $status = 'LULUS';
                $kualifikasi = '';
                if ($diklat->category_id == 1) {
                    if ((int) $certificate->nilai >= 92.5) {
                        $nilai = 'Sangat Memuaskan';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 92.5 && (int) $certificate->nilai >= 85) {
                        $nilai = 'Memuaskan';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 85 && (int) $certificate->nilai >= 77.5) {
                        $nilai = 'Sangat Baik';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 77.5 && (int) $certificate->nilai >= 70) {
                        $nilai = 'Baik';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 70 && (int) $certificate->nilai >= 60) {
                        $nilai = 'Cukup';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $certificate->nilai < 60) {
                        $nilai = 'Telah Mengikuti';
                        $kualifikasi = strtoupper($nilai);
                    }
                } else {
                    $kualifikasi = strtoupper($nilai);
                }

            if($certificate->is_remedial == 1){
                    $nilai_fix =  'Cukup';
                    
                }else{
                    $nilai_fix = $kualifikasi;
                }

                $data = [
                    'user' => $user,
                    'diklat' => $diklat,
                    'nilai' => $nilai,
                    'detail' => $detail,
                    'absensi' => $absensi,
                    'certificate' => $certificate,
                    'certificateSetting' => $certificateSetting
                ];
            $dompdf = PDF::loadView('frontend.sertificate.transkip', $data);
            /* (Optional) Setup the paper size and orientation */
            $dompdf->setPaper('a4');
            /* Output the generated PDF to Browser */
            return $dompdf->stream();
            // $file = $certificate->id.'.pdf';
            // return  Redirect::url('digital-signatures-transkip/',$file);


        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function bulkUploadNoCertificate(Request $request)
    {
        
        try {
            $folderPath = 'public/no-certificate';
            $diklatId = $request->diklat;
            $diklatDetailId = $request->angkatan;
            $currentTimestamp = Carbon::now()->timestamp;
            if (Storage::disk('local')->exists($folderPath)) {
                Storage::disk('local')->makeDirectory($folderPath, 0775, true);
            }
            if ($request->hasFile('file')) {
                $reqFile = $request->file('file');
                $fileName = Str::slug($reqFile->hashName());
                $ext = $reqFile->extension();
                $renameFileAs = "$currentTimestamp-$fileName.$ext";
                $uploadedExcelFile = Storage::disk('local')->putFileAs($folderPath, $reqFile, $renameFileAs);
                $outputFilePath = storage_path("app/$uploadedExcelFile");
                if ($request->type) {
                    $type = $request->type;
                }else{
                    $type = "online"; 
                }
                // if ($request->type2) {
                //     $type2 = $request->type2;
                // }else{
                //     $type2 = "";
                // }
                
                Nosertif::where('status', 1)->delete();
                $bobot = DiklatBobot::where('diklat_id',$diklatId)->first();
                if(empty($bobot)){
                    DiklatBobot::create(array('custom_name_sertif' => $request->custom_name_sertif,'diklat_id'=>$diklatId));
                }else{
                    DiklatBobot::where('diklat_id',$diklatId)->update(array('custom_name_sertif' => $request->custom_name_sertif));    
                }
                
                $date = date('Y-m-d H:i:s');

                DiklatDetail::where('diklat_id',$diklatId)->where('id',$diklatDetailId)->update(['updated_at'=>$date,'notif'=>1]);
                $sertifikat = Certificate::where('diklat_id',$diklatId)->where('diklat_detail_id',$diklatDetailId)->update(array('status'=>'0'));
                $sertif = Certificate::where('diklat_id',$diklatId)->where('diklat_detail_id',$diklatDetailId)->get();

                Excel::import(new NoCertificateImport($diklatId, $diklatDetailId), $outputFilePath);
                $tej = Certificate::where('diklat_id',$diklatId)->where('diklat_detail_id',$diklatDetailId)->where('no_certificate','empty')->first();
                $te = Certificate::where('diklat_id',$diklatId)->where('diklat_detail_id',$diklatDetailId)->where('no_certificate','empty')->get();
                foreach ($te as $t) {
                    $userss = User::find($t->user_id);
                    Nosertif::insert(
                       array(
                        
                        'nip'   =>  $userss->username,
                        'status'=> 1
                    )
                   );

                }
                foreach ($sertif as $cek) {
                    if (isset($tej)) {
                        Certificate::where('diklat_id',$diklatId)->where('diklat_detail_id',$diklatDetailId)->update(array('no_certificate'=>'empty'));
                        $user = User::find($cek->user_id);
                    
                        return redirect()
                    ->route('voyager.certificates.index', ['diklatId' => $diklatId, 'angkatanId' => $diklatDetailId])
                    ->with('pesan', "NIP tidak sesuai, mohon dicek kembali !");

                    }


                }

                // foreach ($sertif as $sertifikat1) {
                //    $this->downloadCertificate($sertifikat1->id);
                //    $this->downloadTranskip($sertifikat1->id);
                   
                // }
                
                // $get_diklat = Diklat::where('id',$diklatId)->first();
                // $get_angkatan = DiklatDetail::where('id',$diklatDetailId)->first();
                // $get_email = CertificateSetting::where('id',1)->first();
                // if (!empty($get_email->approval_email)) {
                //    $mail = $get_email->approval_email;
                // }else{
                //     $mail = "anakdesa088@gmail.com";
                // }
                // $button = '<br><a class="btn btn-primary" href="'.url('admin/digital-signatures/tandatangan?relation%5BgetDiklat%5D%5B%5D='.$diklatId.'&relation%5BgetDiklatDetail%5D%5B%5D='.$diklatDetailId.'').'">Lihat Selengkapnya</a>';
                
                // $mes = $get_email->content_mail.$button;
                // $subject = $get_email->subject_mail;
                

                // Mail::send([], ['name', 'Permintaan Approve Didital Signature'], function ($message) use ($mail, $mes,$subject) {
                //     $message->to($mail)
                //     ->subject($subject)
                //     ->setBody((string)view('vendor.mail.custom-mail')->withMes($mes)->withUser($mail), 'text/html');
                // });
                
                return redirect()
                    ->route('voyager.certificates.index', 
                        [
                            'diklatId' => $diklatId, 
                            'angkatanId' => $diklatDetailId,
                            'custom_name_sertif' => $request->custom_name_sertif
                        ])
                    ->with([
                        'message'    => "Berhasil mengupload nomor sertifikat",
                        'alert-type' => 'success',
                    ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    
    public function sendDc(Request $request)
    {
        

            $diklatId = $request->diklat_id;
            $diklatDetailId = $request->diklat_detail_id;
            $currentTimestamp = Carbon::now()->timestamp;
            
                
                
                
                $date = date('Y-m-d H:i:s');

                DiklatDetail::where('diklat_id',$diklatId)->where('id',$diklatDetailId)->update(['updated_at'=>$date,'notif'=>1]);
                // $sertifikat = Certificate::where('diklat_id',$diklatId)->where('diklat_detail_id',$diklatDetailId)->update(array('status'=>'0'));
                $sertif = Certificate::where('diklat_id',$diklatId)->where('diklat_detail_id',$diklatDetailId)->get();

                
                // $sertifikats = Certificate::find();  

                foreach ($sertif as $sertifikat1) {
                    if($sertifikat1->status == 0){
                        $this->downloadCertificate($sertifikat1->id);
                        $this->downloadTranskip($sertifikat1->id);     
                    }
                   
                   
                }
                
                $get_diklat = Diklat::where('id',$diklatId)->first();
                $get_angkatan = DiklatDetail::where('id',$diklatDetailId)->first();
                $get_email = CertificateSetting::where('id',1)->first();
                if (!empty($get_email->approval_email)) {
                   $mail = $get_email->approval_email;
                }else{
                    $mail = "anakdesa088@gmail.com";
                }
                $button = '<br><a class="btn btn-primary" href="'.url('admin/digital-signatures/tandatangan?relation%5BgetDiklat%5D%5B%5D='.$diklatId.'&relation%5BgetDiklatDetail%5D%5B%5D='.$diklatDetailId.'').'">Lihat Selengkapnya</a>';
                
                $mes = $get_email->content_mail.$button;
                $subject = $get_email->subject_mail;
                

                $result =  Mail::send([], ['name', 'Permintaan Approve Didital Signature'], function ($message) use ($mail, $mes,$subject) {
                    $message->to($mail)
                    ->subject($subject)
                    ->setBody((string)view('vendor.mail.custom-mail')->withMes($mes)->withUser($mail), 'text/html');
                });
                
                if($result){


                    return response()->json(['success'=>'Sertifikat $request->sertifikat_id']);    
                }else{
                    return response()->json(['error'=>'Sertifikat Gagal Disetujui']);    
                }
           
    }
    public function email(){
        Mail::send([], ['name', 'Permintaan Approve Didital Signature'], function ($message) use ($mail, $mes,$subject) {
                    $message->to($mail)
                    ->subject($subject)
                    ->setBody((string)view('vendor.mail.custom-mail')->withMes($mes)->withUser($mail), 'text/html');
                });
    }
    public function getNotif($diklat_id,$diklat_detail_id){
        $detail = DiklatDetail::where('diklat_id',$diklat_id)->where('id',$diklat_detail_id)->update(['notif'=>0]);
        return redirect(url('admin/digital-signatures/tandatangan?relation%5BgetDiklat%5D%5B%5D='.$diklat_id.'&relation%5BgetDiklatDetail%5D%5B%5D='.$diklat_detail_id.''));
    }
    public function downloadCertificate($sertif_id)
    {  
         
        $certificate = Certificate::where('id','=',$sertif_id)->first();
        $user = User::find($certificate->user_id);
        $diklat = Diklat::find($certificate->diklat_id);
                $detail = DiklatDetail::find($certificate->diklat_detail_id);
                $absensi = $certificate->absen;
                $certificateSetting = CertificateSetting::first();
                $nilai = 'Telah Mengikuti';
                $status = '';
                $kualifikasi = '';
                if ($diklat->category_id == 1) {
                    if ((int) $certificate->nilai >= 92.5) {
                        $nilai = 'Sangat Memuaskan';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 92.5 && (int) $certificate->nilai >= 85) {
                        $nilai = 'Memuaskan';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 85 && (int) $certificate->nilai >= 77.5) {
                        $nilai = 'Sangat Baik';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 77.5 && (int) $certificate->nilai >= 70) {
                        $nilai = 'Baik';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 70 && (int) $certificate->nilai >= 60) {
                        $nilai = 'Cukup';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai < 60) {
                        $nilai = 'Telah Mengikuti';
                        $kualifikasi = strtoupper($nilai);
                    }
                } else {
                    $kualifikasi = strtoupper($nilai);
                }
                $data = [
                    'user' => $user,
                    'diklat' => $diklat,
                    'nilai' => $kualifikasi,
                    'detail' => $detail,
                    'absensi' => $absensi,
                    'certificate' => $certificate,
                    'certificateSetting' => $certificateSetting
                ];
                $dompdf = PDF::loadView('frontend.sertificate.index', $data);
                /* (Optional) Setup the paper size and orientation */
                $dompdf->setPaper('a4', 'landscape');
                $path_url = 'sertif/'.$sertif_id.'.pdf';
                return $dompdf->save(public_path($path_url), array("Attachment" => true));
                
    }  
      public function downloadser($sertif_id)
    {  
         
        $certificate = Certificate::where('id','=',$sertif_id)->first();
        $user = User::find($certificate->user_id);
        $diklat = Diklat::find($certificate->diklat_id);
                $detail = DiklatDetail::find($certificate->diklat_detail_id);
                $absensi = $certificate->absen;
                $certificateSetting = CertificateSetting::first();
                $nilai = 'Telah Mengikuti';
                $status = '';
                $kualifikasi = '';
                if ($diklat->category_id == 1) {
                    if ((int) $certificate->nilai >= 92.5) {
                        $nilai = 'Sangat Memuaskan';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 92.5 && (int) $certificate->nilai >= 85) {
                        $nilai = 'Memuaskan';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 85 && (int) $certificate->nilai >= 77.5) {
                        $nilai = 'Sangat Baik';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 77.5 && (int) $certificate->nilai >= 70) {
                        $nilai = 'Baik';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 70 && (int) $certificate->nilai >= 60) {
                        $nilai = 'Cukup';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai < 60) {
                        $nilai = 'Telah Mengikuti';
                        $kualifikasi = strtoupper($nilai);
                    }
                } else {
                    $kualifikasi = strtoupper($nilai);
                }
                $data = [
                    'user' => $user,
                    'diklat' => $diklat,
                    'nilai' => $kualifikasi,
                    'detail' => $detail,
                    'absensi' => $absensi,
                    'certificate' => $certificate,
                    'certificateSetting' => $certificateSetting
                ];
                $dompdf = PDF::loadView('frontend.sertificate.index', $data);
                /* (Optional) Setup the paper size and orientation */
                $dompdf->setPaper('a4', 'landscape');
                $path_url = 'sertif/'.$sertif_id.'.pdf';
                return $dompdf->save(public_path($path_url), array("Attachment" => true));
                
    }  
    

    public function downloadTranskip($sertif_id)
    {  
         
        $certificate = Certificate::where('id','=',$sertif_id)->first();
        $user = User::find($certificate->user_id);
        $diklat = Diklat::find($certificate->diklat_id);
                $detail = DiklatDetail::find($certificate->diklat_detail_id);
                $absensi = $certificate->absen;
                $certificateSetting = CertificateSetting::first();
                $nilai = 'Telah Mengikuti';
                $status = '';
                $kualifikasi = '';
                if ($diklat->category_id == 1) {
                    if ((int) $certificate->nilai >= 92.5) {
                        $nilai = 'Sangat Memuaskan';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 92.5 && (int) $certificate->nilai >= 85) {
                        $nilai = 'Memuaskan';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 85 && (int) $certificate->nilai >= 77.5) {
                        $nilai = 'Sangat Baik';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 77.5 && (int) $certificate->nilai >= 70) {
                        $nilai = 'Baik';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 70 && (int) $certificate->nilai >= 60) {
                        $nilai = 'Cukup';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai < 60) {
                        $nilai = 'Telah Mengikuti';
                        $kualifikasi = strtoupper($nilai);
                    }
                } else {
                    $kualifikasi = strtoupper($nilai);
                }
                $data = [
                    'user' => $user,
                    'diklat' => $diklat,
                    'nilai' => $kualifikasi,
                    'detail' => $detail,
                    'absensi' => $absensi,
                    'certificate' => $certificate,
                    'certificateSetting' => $certificateSetting
                ];
                
                $dompdf = PDF::loadView('frontend.sertificate.transkip', $data);
                /* (Optional) Setup the paper size and orientation */
                $dompdf->setPaper('a4');
                $path_url = 'transkip/'.$sertif_id.'.pdf';
                return $dompdf->save(public_path($path_url), array("Attachment" => true));
                
    }  

    public function upload_doc($diklat_id,$diklat_detail_id,$user_id){
        $sertif = Certificate::where('diklat_id',$diklat_id)->where('diklat_detail_id',$diklat_detail_id)->where('user_id',$user_id)->first();

        $path = public_path('sertif/');        

        $user = User::find($sertif->user_id);
        $asal = $path.$sertif->id.'.pdf';
        if(isset($asal)){
            $url = 'http://10.2.237.167/api/sign/pdf';

            $url_signed = 'http://10.2.237.167/api/sign/pdf';

            $curl = curl_init();



            curl_setopt_array($curl, array(
                CURLOPT_URL => $url_signed,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => array('file'=> new \CURLFILE($asal,'application/pdf'),'nik' => $user->username,'passphrase' => 'Coba saja ya','tampilan' => 'invisible','page' => '1','image' => 'false','linkQR' => 'https://google.com','xAxis' => '0','yAxis' => '0','width' => '200','height' => '100','jenis_response'=>'BASE64'),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic ". base64_encode('eskopi:Pusd1klat2021!')

                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
            curl_close($curl);
            $result = json_decode($response, true);
            if($httpCode == 200){
                $pecah = explode('"id_dokumen":"',$response);
                $id_dokumen = str_replace('","message":"proses berhasil","signing_time":null}','',$pecah[1]);
                Certificate::where('diklat_id',$diklat_id)->where('diklat_detail_id',$diklat_detail_id)->where('user_id',$sertif->user_id)->update(['id_dokumen'=>$id_dokumen]);
                
                $this->download_doc($id_dokumen);
            }else{
                return $response;
            }

        }
        
    }

    public function download_doc($id){
         $curl = curl_init();
    $get_id = Certificate::where('id_dokumen',$id)->first();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://10.2.237.167/api/sign/download/".$id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(

            "authorization: Basic ". base64_encode('eskopi:Pusd1klat2021!'),
            "cache-control: no-cache",
            "postman-token: 64da5790-49e2-5c2a-40ec-78401c279ce2"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $hasil = json_decode($response);
    
    $dir = public_path('digital-signatures');
    $file_name = $dir.'/'.$get_id->id.'.pdf';
    
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        if (!isset($hasil->error)) {
              
                 $decoded_file_data = base64_decode($response);

    
    file_put_contents($file_name, $response);
                 
              

        }else{
            return $response;
        }
    }
    }

    function download($filename, $filepath, $base64_encoded_file_data) {
    // Prevents run-out-of-memory issue
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Decodes encoded data
    $decoded_file_data = base64_decode($base64_encoded_file_data);

    // Writes data to the specified file
    file_put_contents($filepath, $base64_encoded_file_data);

    // header('Expires: 0');
    // header('Pragma: public');
    // header('Cache-Control: must-revalidate');
    // header('Content-Length: ' . filesize($filepath));
    // header('Content-Type: application/octet-stream');
    // header('Content-Disposition: attachment; filename="' . $filename . '"');
    // readfile($filepath);
    
    // Deletes the temp file
    
}
// public function generateCertificatee(Certificate $certificate)
//     {
//         $user = User::find($certificate->user_id);
//         if ($user->avatar == 'users/default.png') {
//             session()->flash('warning_msg', "Anda harus mengganti foto profil anda terlebih dahulu.");
//             return back();
//         } else {
//             try {
//                 if (!file_exists(storage_path('app/public/' . $user->avatar))) {
//                     return dd("File photo user tidak ada, silahkan update terlebih dahulu photo user");
//                 }
//                 $diklat = Diklat::find($certificate->diklat_id);
//                 $detail = DiklatDetail::find($certificate->diklat_detail_id);
//                 $absensi = $certificate->absen;
//                 $certificateSetting = CertificateSetting::first();
//                        $nilai = 'Telah Mengikuti';
//                 $status = '';
//                 $kualifikasi = '';
//                 if ($diklat->category_id == 1) {
//                     if ((int) $certificate->nilai >= 92.5) {
//                         $nilai = 'Sangat Memuaskan';
//                         $kualifikasi = $status . '  ' . strtoupper($nilai);
//                     } elseif ((int) $certificate->nilai <= 92.5 && (int) $certificate->nilai >= 85) {
//                         $nilai = 'Memuaskan';
//                         $kualifikasi = $status . '  ' . strtoupper($nilai);
//                     } elseif ((int) $certificate->nilai <= 85 && (int) $certificate->nilai >= 77.5) {
//                         $nilai = 'Sangat Baik';
//                         $kualifikasi = $status . '  ' . strtoupper($nilai);
//                     } elseif ((int) $certificate->nilai <= 77.5 && (int) $certificate->nilai >= 70) {
//                         $nilai = 'Baik';
//                         $kualifikasi = $status . '  ' . strtoupper($nilai);
//                     } elseif ((int) $certificate->nilai <= 70 && (int) $certificate->nilai >= 60) {
//                         $nilai = 'Cukup';
//                         $kualifikasi = $status . '  ' . strtoupper($nilai);
//                     } elseif ((int) $certificate->nilai < 60) {
//                         $nilai = 'Telah Mengikuti';
//                         $kualifikasi = strtoupper($nilai);
//                     }
//                 } else {
//                     $kualifikasi = strtoupper($nilai);
//                 }
//                 if($certificate->is_remedial == 1){
//                     $nilai_fix =  'Cukup';
//                 }else{
//                     $nilai_fix = $kualifikasi;
//                 }
//                 $data = [
//                     'user' => $user,
//                     'diklat' => $diklat,
//                     'nilai' => $nilai_fix,
//                     'detail' => $detail,
//                     'absensi' => $absensi,
//                     'certificate' => $certificate,
//                     'certificateSetting' => $certificateSetting
//                 ];
//                 $dompdf = PDF::loadView('frontend.sertificate.index', $data);
//                 /* (Optional) Setup the paper size and orientation */
//                 $dompdf->setPaper('a4', 'landscape');
//                 /* Output the generated PDF to Browser */
//                 return $dompdf->stream();
//                 // return view('frontend.sertificate.test', $data);
//             } catch (\Throwable $th) {
//                 dd($th);
//             }
//         }
//     }
// }

public function generateCertificatee(Certificate $certificate)
    {
        $user = User::find($certificate->user_id);
        if ($user->avatar == 'users/default.png') {
            session()->flash('warning_msg', "Anda harus mengganti foto profil anda terlebih dahulu.");
            return back();
        } else {
            try {
                if (!file_exists(storage_path('app/public/' . $user->avatar))) {
                    return dd("File photo user tidak ada, silahkan update terlebih dahulu photo user");
                }
                $diklat = Diklat::find($certificate->diklat_id);
                $detail = DiklatDetail::find($certificate->diklat_detail_id);
                $absensi = $certificate->absen;
                $certificateSetting = CertificateSetting::first();
                       $nilai = 'Telah Mengikuti';
                $status = '';
                $kualifikasi = '';
                if ($diklat->category_id == 1) {
                    if ((int) $certificate->nilai >= 92.5) {
                        $nilai = 'Sangat Memuaskan';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 92.5 && (int) $certificate->nilai >= 85) {
                        $nilai = 'Memuaskan';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 85 && (int) $certificate->nilai >= 77.5) {
                        $nilai = 'Sangat Baik';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 77.5 && (int) $certificate->nilai >= 70) {
                        $nilai = 'Baik';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai <= 70 && (int) $certificate->nilai >= 60) {
                        $nilai = 'Cukup';
                        $kualifikasi = $status . '  ' . strtoupper($nilai);
                    } elseif ((int) $certificate->nilai < 60) {
                        $nilai = 'Telah Mengikuti';
                        $kualifikasi = strtoupper($nilai);
                    }
                } else {
                    $kualifikasi = strtoupper($nilai);
                }
                if($certificate->is_remedial == 1){
                    $nilai_fix =  'Cukup';
                }else{
                    $nilai_fix = $kualifikasi;
                }
                $data = [
                    'user' => $user,
                    'diklat' => $diklat,
                    'nilai' => $nilai,
                    'detail' => $detail,
                    'absensi' => $absensi,
                    'certificate' => $certificate,
                    'certificateSetting' => $certificateSetting
                ];
                $dompdf = PDF::loadView('frontend.sertificate.index', $data);
                /* (Optional) Setup the paper size and orientation */
                $dompdf->setPaper('a4', 'landscape');
                /* Output the generated PDF to Browser */
                return $dompdf->stream();
                // return view('frontend.sertificate.test', $data);
            } catch (\Throwable $th) {
                dd($th);
            }
        }
    }
}