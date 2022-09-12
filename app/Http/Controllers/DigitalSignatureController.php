<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
 
namespace App\Http\Controllers;
use GuzzleHttp\Client;
use PDF;
use App\DaerahKbupaten;
use App\DaerahProvinsi;
use App\Dept;
use App\User;
use App\Diklat;
use App\DiklatDetail;
use App\DigitalSignature;
use App\MataDiklat;
use App\Certificate;
use App\CertificateSetting;
use App\Exports\ReportUserExport;
use App\Exports\AbensiExport;
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
use App\Position;
use App\ErrorDc;
use App\SurveyFeedback;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailLulus;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as MaatwebsiteExcel;
use Maatwebsite\Excel\Facades\Excel;

class DigitalSignatureController extends VController
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
    private $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(Request $request)
    {
        // $this->authorize('browse', 'digital-signatures');
        $reports = json_encode(['tandatangan','laporan']);
        $view = "voyager::digital-signatures.index";
        return view($view)->withReports(json_decode($reports));



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

    public function show(Request $request, string $slug)
    {
              // Add data filter

                $datas = $this->{str_replace('-', '', $slug)}();
                
                    $dataType = Voyager::model('DataType')->where('slug', '=', 'certificates')->first();
                

                
                // $this->authorize('browse', app($dataType->model_name));
                // Get data
                $table = call_user_func([&$this, 'getTables'], $request, $dataType, $slug, $datas);
                foreach ($table as $key => $value) {
                    ${$key} = $value;
                }
                
                // Check if server side pagination is enabled
             
                // Check if a default search key is set
                $defaultSearchKey = $dataType->default_search_key ?? null;

                $view = "voyager::digital-signatures.$slug.browse";
                if ($method == 'get') {
                    $views = Voyager::view($view, compact(
                        'dataType',
                        'dataTypeContent',
                        'isModelTranslatable',
                        'search',
                        'orderBy',
                        'orderColumn',
                        'sortOrder',
                        'searchable',
                       
                        'defaultSearchKey',
                        'usesSoftDeletes',
                        'showSoftDeleted',
                        'datas',
                        'slug'
                    ));
                } else {
                    $views = compact(
                        'dataType',
                        'dataTypeContent',
                        'search',
                        'slug'
                    );
                }

                return $views;
            
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

        $dataType = Voyager::model('DataType')->where('slug', '=', 'users')->first();

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

    public function getKonfirm(Request $request)
    {
        $data = app('App\User')->whereHas('getDiklatDetail', function($builder){
            $builder->where('diklat_detail_users.status', 0)->where('diklat_detail_users.file', '=', null);
        })->whereStatus('pending')->get();
        // dd($data);
        return view('vendor.voyager.users.user-konfirm', compact('data'));
    }

    public function getDocument(Request $request)
    {
        $data = app('App\User')->whereHas('getDiklatDetail', function($builder){
            $builder->where('diklat_detail_users.status', 1)->where('diklat_detail_users.file', '!=', null);
        })->whereStatus('active')->get();
        // dd($data);
        return view('vendor.voyager.users.user-document', compact('data'));
    }

    public function postKonfirm(Request $request, $id, $diklat)
    {
        $user = app('App\User')->find($id);
        $mes = $request->message;
        if ($request->type == 'attach') {
            $user->status = 'active';
            $user->save();
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
        } else {
            $user->status = 'blacklist';
            $user->save();
            $sendMail = Mail::send([], ['name', 'Admin Pendaftaran Diklat E-Learning BKPM'], function ($message) use ($user, $mes) {
                $message->to($user->email)
                    ->subject('Permintaan Pendaftaran Anda Kami Tolak. Anda di blacklist!')
                    ->setBody((string)view('vendor.mail.custom-mail')->withMes($mes)->withUser($user), 'text/html');
            });
        }
        $data = [
            'message'    => "User {$user->name} berhasil di edit",
            'alert-type' => 'success',
        ];
        // dd($data);
        return back()->withData($data);
    }

    public function postDocument(Request $request, $id, $diklat)
    {
        // dd($id, $diklat);
        $user = app('App\User')->find($id);
        $mes = $request->message;
        if ($request->type == 'attach') {
            $detail = app('App\DiklatDetailUser')->find($diklat);
            $detail->status = 2;
            $detail->save();
            Mail::send([], ['name', 'Admin Pendaftaran Diklat E-Learning BKPM'], function ($m) use ($user, $mes) {
                if ($this->request->hasFile('attachment')) {
                    $publicPath = Storage::putFile('public/mail/konfirm-email', $this->request->file('attachment'));
                    $path = Storage::url($publicPath);
                    $m->attach(public_path().$path);
                }
                $m->to($user->email);
                $m->subject('Permintaan Pendaftaran Anda Telah Di Setujui.');
                $m->setBody((string)view('vendor.mail.custom-mail')->withMes($mes)->withUser($user), 'text/html');
            });
        } else {
            $user->status = 'blacklist';
            $user->save();
            Mail::send([], ['name', 'Admin Pendaftaran Diklat E-Learning BKPM'], function ($message) use ($user, $mes) {
                $message->to($user->email)
                    ->subject('Permintaan Pendaftaran Anda Kami Tolak. Anda di blacklist!')
                    ->setBody((string)view('vendor.mail.custom-mail')->withMes($mes)->withUser($user), 'text/html');
            });
        }
        $data = [
            'message'    => "User {$user->name} berhasil di edit",
            'alert-type' => 'success',
        ];
        return back()->withData($data);
    }

    function getTables($request, $dataType, $slug, $datas)
    {
        $method = strtolower($request->getMethod());
        $getter = (hash_equals($method, "post")) ? 'get' : (($dataType->server_side) ? 'get' : 'get') ;
        $search = (object) ['relation' => $request->{$method}('relation'), 'inline' => $request->{$method}('inline')];
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

            //  Call filter by slug
            call_user_func(array(&$this, 'filter'.str_replace('App\\', '', $dataType->model_name)), $query, $search, $slug, $datas);

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                if($slug == 'virtual-class-absents')
                {
                    $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
                }
                else
                {
                    $dataTypeContent = call_user_func([
                        $query->with('getDiklatDetail')->orderBy($orderBy, $querySortOrder),
                        $getter,
                    ]);
                }
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

        return [
            'dataTypeContent' => $dataTypeContent,
            'isModelTranslatable' => $isModelTranslatable,
            'search' => $search,
            'orderBy' => $orderBy,
            'orderColumn' => $orderColumn,
            'sortOrder' => $sortOrder,
            'searchable' => $searchable,
            'usesSoftDeletes' => $usesSoftDeletes,
            'showSoftDeleted' => $showSoftDeleted,
            'method' => $method
        ];
    }

    public static function users()
    {
        $diklat = Diklat::with('diklatDetail')->orderByDesc('created_at')->get();
    $angkatan = DiklatDetail::orderByDesc('created_at')->get();
    $dept1 = Dept::select('title')->orderByDesc('created_at')->get();
    $jabatan = Position::select('title')->orderByDesc('created_at')->get();
    $kota = DaerahKbupaten::select('nama')->orderByDesc('created_at')->get();
    $prov = DaerahProvinsi::select('nama')->orderByDesc('created_at')->get();
    $nama = User::select('name')->orderByDesc('name')->get();
    $users = User::orderByDesc('name')->get();
    $nip = User::select('username','id')->orderByDesc('created_at')->get();
    return $data = [
        'diklat' => $diklat,
        'angkatan'=>$angkatan,
        'dept1' => $dept1,
        'jabatan' => $jabatan,
        'kota' => $kota,
        'prov' => $prov,
        'nama'=>$nama,
        'users' =>$users,
        'nip'=>$nip,
            'fieldShow' => [
                'name',
                'category_id',
                'username',
                'email',
                'mobile',
               // 'dept',
               // 'position'
               
            ]
        ];
    }

    public function pretests()
    {
       $diklat = Diklat::with('postest')->orderByDesc('created_at')->get();
        
        $dept = Dept::select('title')->orderByDesc('created_at')->get();
         $angkatan = DiklatDetail::orderByDesc('created_at')->get();
        $position = Position::select('title')->orderByDesc('created_at')->get();
        $kota = DaerahKbupaten::select('nama')->orderByDesc('created_at')->get();
        $prov = DaerahProvinsi::select('nama')->orderByDesc('created_at')->get();
        return $data = [
            'diklat' => $diklat,
            // 'detail' => $detail,
            'dept' => $dept,
            'angkatan'=> $angkatan,
            'position' => $position,
            'fieldShow' => [
                'name',
                'category_id',
                'username',
                'email',
                'mobile',
                'user_belongsto_dept_relationship',
                'user_belongsto_position_relationship',
            ]
        ];
    }

    public function postests()
    {
        $diklat = Diklat::with('postest')->orderByDesc('created_at')->get();
        
        $dept = Dept::select('title')->orderByDesc('created_at')->get();
         $angkatan = DiklatDetail::orderByDesc('created_at')->get();
        $position = Position::select('title')->orderByDesc('created_at')->get();
        $kota = DaerahKbupaten::select('nama')->orderByDesc('created_at')->get();
        $prov = DaerahProvinsi::select('nama')->orderByDesc('created_at')->get();
        return $data = [
            'diklat' => $diklat,
            // 'detail' => $detail,
            'dept' => $dept,
            'angkatan'=> $angkatan,
            'position' => $position,
            'fieldShow' => [
                'name',
                'category_id',
                'username',
                'email',
                'mobile',
                'user_belongsto_dept_relationship',
                'user_belongsto_position_relationship',
            ]
        ];
    }

    public function surveyfeedback(Type $var = null)
    {
        $diklat = Diklat::orderByDesc('created_at')->get();
        $survey = SurveyFeedback::orderByDesc('created_at')->get();
        $dept = Dept::select('title')->orderByDesc('created_at')->get();
        $position = Position::select('title')->orderByDesc('created_at')->get();
        $kota = DaerahKbupaten::select('nama')->orderByDesc('created_at')->get();
        $prov = DaerahProvinsi::select('nama')->orderByDesc('created_at')->get();
        return [
            'diklat' => $diklat,
            'survey' => $survey,
            'dept' => $dept,
            'position' => $position,
            'kota' => $kota,
            'prov' => $prov,
            'fieldShow' => [
                'name',
                'category_id',
                'username',
                'email',
                'mobile',
                'user_belongsto_dept_relationship',
                'user_belongsto_position_relationship',
            ]
        ];
    }

    public function virtualclassabsents()
    {
        $diklat = Diklat::orderByDesc('created_at')->get();

        return [
            'diklat' => $diklat,
            'fieldShow' => [
                'signature',
                'virtual_class_absent_belongsto_user_relationship', 
                'virtual_class_absent_belongsto_user_relationship_1',
                'virtual_class_absent_belongsto_user_relationship_2',
            ]
        ];   
    }

     public function tandatangan(){
        $diklat_id = 
        $diklat = Diklat::with('diklatDetail')->orderByDesc('created_at')->get();
        $dept = Dept::select('title')->orderByDesc('created_at')->get();
         $angkatan = DiklatDetail::orderByDesc('created_at')->get();
        $position = Position::select('title')->orderByDesc('created_at')->get();
        $kota = DaerahKbupaten::select('nama')->orderByDesc('created_at')->get();
        $prov = DaerahProvinsi::select('nama')->orderByDesc('created_at')->get();
        return $data = [
            'diklat' => $diklat,
            'dept' => $dept,
            'angkatan'=> $angkatan,
            'position' => $position,
            'kota' => $kota,
            'prov' => $prov,
            'fieldShow' => [
                'name',
                'category_id',
                'username',
                'email',
                'mobile',
                
            ]
        ];
    }
   
    public function laporan(){
        $diklat = Diklat::with('diklatDetail')->orderByDesc('created_at')->get();
        $dept = Dept::select('title')->orderByDesc('created_at')->get();
         $angkatan = DiklatDetail::orderByDesc('created_at')->get();
        $position = Position::select('title')->orderByDesc('created_at')->get();
        $kota = DaerahKbupaten::select('nama')->orderByDesc('created_at')->get();
        $prov = DaerahProvinsi::select('nama')->orderByDesc('created_at')->get();
        return $data = [
            'diklat' => $diklat,
            // 'detail' => $detail,
            'dept' => $dept,
            'angkatan'=> $angkatan,
            'position' => $position,
            'kota' => $kota,
            'prov' => $prov,
            'fieldShow' => [
                'name',
                'category_id',
                'username',
                'email',
                'mobile',
                'user_belongsto_dept_relationship',
                'user_belongsto_position_relationship',
            ]
        ];
    }


   
    function filterCertificate($query, $search, $slug, $datas)
    {
        // dd($datas);
        // if (!empty($search->inline) || !empty($search->relation)) {
            switch ($slug) {
                    case 'laporan':
                    if (!empty($search->relation)) {
                        foreach ($search->relation as $kr => $vr) {
                            if ((string)$kr == 'getDiklat') {
                                $query->whereIn('diklat_id', $vr);
                            }
                            
                            

                        }
                    }


                    break;
                    case 'tandatangan':
                    if (!empty($search->relation)) {
                        foreach ($search->relation as $kr => $vr) {
                            if ((string)$kr == 'getDiklat') {
                                $query->whereIn('diklat_id', $vr);
                            }
                            
                            

                        }
                    }
                    break;

            }
        // }
        return $query;
    }
// $username, $passphrase, $file_surat, $teks_qr, $file_gambar_ttd = false
   

    public function toPDF(Request $request, string $slug)
    {
        $data = call_user_func(array(&$this, 'show'), $request, $slug);
        $pathFile = Storage::disk(config('voyager.storage.disk'))->url('laporan/Laporan_'.ucwords($slug).'_'.strftime('%d_%B_%Y_%H%m%s').'.pdf');

        $datas = (array)$data;
        $fields = $request->filter;
        $pdf = '';
        
        $no = 0;

        foreach($datas as $k => $v)
        {
            $no += 1;

            if($no == 4)
            {
                $v = (object)$v;
                $pdf = PDF::loadView('voyager::exports.child.virtual-class-absents', ['datas' => $v, 'fields' => $fields]);

                break;
            }
        }

        return $pdf->download('Laporan_'.ucwords($slug).'_'.strftime('%d_%B_%Y_%H%m%s').'.pdf');
    }

    function carbonConvert($date)
    {
        return Carbon::parse($date);
    }
 
function http_build_query( $query ){

    $query_array = array();

    foreach( $query as $key => $key_value ){

        $query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );

    }

    return implode( '&', $query_array );

}

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

    public function approvedPeserta(Request $request){



             $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $sertifikat = Certificate::find($id);
            // $this->downloadCertificate($id);
            $this->upload_doc($sertifikat->diklat_id,$sertifikat->diklat_detail_id,$sertifikat->user_id,$request->text,$request->passphrase);
             $this->upload_doc_transkip($sertifikat->diklat_id,$sertifikat->diklat_detail_id,$sertifikat->user_id,$request->text,$request->passphrase);

            $update = Certificate::where('diklat_id',$sertifikat->diklat_id)->where('diklat_detail_id',$sertifikat->diklat_detail_id)->where('id',$id)->update(array('status' => '1'));
            

            $path = public_path('digital-signatures/');    
            $asal = $path.$id.'.pdf';

            $url_signed = 'http://10.2.237.166/api/sign/verify';
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
                CURLOPT_POSTFIELDS => array('signed_file'=> new \CURLFILE($asal,'application/pdf')),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic ZXNpZ246cXdlcnR5",

                ),
            ));

            $response = curl_exec($curl);

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); 

            curl_close($curl);

            $result = json_decode($response);    
            
   



}

     return redirect('admin/digital-signatures/tandatangan')->with([
    'message'    => "Sertifikat Berhasil Disetujui",
    'alert-type' => 'success',
    ]);
    }

    // public function approved(Request $request){



    //     $sertifikat = Certificate::where('diklat_id',$request->diklat_id)->where('diklat_detail_id',$request->diklat_detail_id)->get();

    //     foreach($sertifikat as $sertif) {
    //         $this->downloadCertificate($sertif->id);
    //         $this->upload_doc($sertif->diklat_id,$sertif->diklat_detail_id,$sertif->user_id,$request->text,$request->passphrase);
    //         $this->upload_doc_transkip($sertif->diklat_id,$sertif->diklat_detail_id,$sertif->user_id,$request->text,$request->passphrase);
    //      $sertif = Certificate::where('diklat_id',$sertif->diklat_id)->where('diklat_detail_id',$sertif->diklat_detail_id)->where('user_id',$sertif->user_id)->first();

    //     $path = public_path('sertif/');        

    //     $user = User::find($sertif->user_id);
    //     $asal = $path.$sertif->id.'.pdf';
    //     if(isset($asal)){
    //         $url = 'http://10.2.237.166/api/sign/pdf';

    //         $url_signed = 'http://10.2.237.166/api/sign/pdf';

    //         $curl = curl_init();



    //         curl_setopt_array($curl, array(
    //             CURLOPT_URL => $url_signed,
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_ENCODING => "",
    //             CURLOPT_MAXREDIRS => 10,
    //             CURLOPT_TIMEOUT => 0,
    //             CURLOPT_FOLLOWLOCATION => true,
    //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //             CURLOPT_CUSTOMREQUEST => "POST",
    //             CURLOPT_POSTFIELDS => array('file'=> new \CURLFILE($asal,'application/pdf'),
    //                 'nik' => '3174047004710002',
    //                 'passphrase' => $request->passphrase,//#1234Qwer*
    //                 'tampilan' => 'invisible',
    //                 'page' => '1',
    //                 'image' => 'false',
    //                 'linkQR' => 'https://google.com',
    //                 'xAxis' => '0',
    //                 'yAxis' => '0',
    //                 'width' => '200',
    //                 'height' => '100',
    //                 'reason'=>$request->text,
    //                 'jenis_response'=>'BASE64'),
    //             CURLOPT_HTTPHEADER => array(
    //                 "Authorization: Basic ". base64_encode('eskopi:Pusd1klat2021!')

    //             ),
    //         ));

    //         $response = curl_exec($curl);
    //         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
    //         curl_close($curl);
    //         $result = json_decode($response, true);
    //         if($httpCode == 200){

    //             $pecah = explode('"id_dokumen":"',$response);
    //             $id_dokumen = str_replace('","message":"proses berhasil","signing_time":null}','',$pecah[1]);
    //             Certificate::where('diklat_id',$request->diklat_id)->where('diklat_detail_id',$request->diklat_detail_id)->where('user_id',$sertif->user_id)->update(['id_dokumen'=>$id_dokumen]);
    //             $diklat_jos = Diklat::where('id',$request->diklat_id)->first();
    //             $this->download_doc($id_dokumen);

    //             Mail::to($user->email)->send(new EmailLulus($diklat_jos->title,$sertif->nilai));

    //         }else{
                  
    //               ErrorDc::create(['response'=>$response]);
    //               return response()->json(['error'=>$response]);

                     


    //         }

    //     }


    //     }

    //     // $cek = Certificate::where('diklat_id',$request->diklat_id)->where('diklat_detail_id',$request->diklat_detail_id)->get();

    //     // $path = public_path('digital-signatures/');        
    //     // foreach ($cek as $sertifs) {

    //     //     $asals = $path.$sertifs->id.'.pdf';

    //     //     $url_signed = 'http://10.2.237.166/api/sign/verify';
    //     //     $curl = curl_init();
    //     //     curl_setopt_array($curl, array(
    //     //         CURLOPT_URL => $url_signed,
    //     //         CURLOPT_RETURNTRANSFER => true,
    //     //         CURLOPT_ENCODING => "",
    //     //         CURLOPT_MAXREDIRS => 10,
    //     //         CURLOPT_TIMEOUT => 0,
    //     //         CURLOPT_FOLLOWLOCATION => true,
    //     //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     //         CURLOPT_CUSTOMREQUEST => "POST",
    //     //         CURLOPT_POSTFIELDS => array('signed_file'=> new \CURLFILE($asals,'application/pdf')),
    //     //         CURLOPT_HTTPHEADER => array(
    //     //             "Authorization: Basic ZXNpZ246cXdlcnR5",

    //     //         ),
    //     //     ));

    //     //     $response = curl_exec($curl);

    //     //     $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); 

    //     //     curl_close($curl);

    //     //     $result = json_decode($response);





    //     // }

        


    //     if($httpCode !== 200){
            
                      
    //                  ErrorDc::create(['response'=>$response]);
    //                  return response()->json(['error'=>$response]);

                     
           
    //     }else{

    //         Certificate::where('diklat_id',$request->diklat_id)->where('diklat_detail_id',$request->diklat_detail_id)->update(array('status' => '1'));
    //         return response()->json(['success'=>'Sertifikat Berhasil Disetujui']);    


    //     }
        








    // }

     public function approved(Request $request){

        if($request->passphrase == 'Eskopi1234&&'){
            $sertifikat = Certificate::where('diklat_id',$request->diklat_id)->where('diklat_detail_id',$request->diklat_detail_id)->get();
            CertificateSetting::where('id',1)->update(['reason'=>$request->text]);
        foreach($sertifikat as $sertif) {
            
             Certificate::where('diklat_id',$sertif->diklat_id)->where('diklat_detail_id',$sertif->diklat_detail_id)->where('user_id',$sertif->user_id)->update(array('status' => '3'));


        }
        return response()->json(['success'=>'Sertifikat Berhasil Disetujui']);  

        }else{
            return response()->json(['error'=>'Passphrase anda salah']);   
        }

        

        


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
                if($certificate->is_remedial == 1){
                    $nilai_fix =  'Cukup';
                }else{
                    $nilai_fix = $kualifikasi;
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

    public function upload_doc($diklat_id,$diklat_detail_id,$user_id,$text,$passphrase){
        $sertif = Certificate::where('diklat_id',$diklat_id)->where('diklat_detail_id',$diklat_detail_id)->where('user_id',$user_id)->first();

        $path = public_path('sertif/');        

        $user = User::find($sertif->user_id);
        $asal = $path.$sertif->id.'.pdf';
        if(isset($asal)){
            $url = 'http://10.2.237.166/api/sign/pdf';

            $url_signed = 'http://10.2.237.166/api/sign/pdf';

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
                CURLOPT_POSTFIELDS => array('file'=> new \CURLFILE($asal,'application/pdf'),
                    'nik' => '3174047004710002',
                    'passphrase' => $passphrase,//#1234Qwer*
                    'tampilan' => 'invisible',
                    'page' => '1',
                    'image' => 'false',
                    'linkQR' => 'https://google.com',
                    'xAxis' => '0',
                    'yAxis' => '0',
                    'width' => '200',
                    'height' => '100',
                    'reason'=>$text,
                    'jenis_response'=>'BASE64'),
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
                  ErrorDc::create(['response'=>$response]);
                  return response()->json(['error'=>$response]);

            }

        }
        
    }
    public function upload_doc_transkip($diklat_id,$diklat_detail_id,$user_id,$text,$passphrase){
        $sertif = Certificate::where('diklat_id',$diklat_id)->where('diklat_detail_id',$diklat_detail_id)->where('user_id',$user_id)->first();

        $path = public_path('transkip/');        

        $user = User::find($sertif->user_id);
        $asal = $path.$sertif->id.'.pdf';
        if(isset($asal)){
            $url = 'http://10.2.237.166/api/sign/pdf';

            $url_signed = 'http://10.2.237.166/api/sign/pdf';

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
                CURLOPT_POSTFIELDS => array('file'=> new \CURLFILE($asal,'application/pdf'),
                    'nik' => '3174047004710002',
                    'passphrase' => $passphrase,//#1234Qwer*
                    'tampilan' => 'invisible',
                    'page' => '1',
                    'image' => 'false',
                    'linkQR' => 'https://google.com',
                    'xAxis' => '0',
                    'yAxis' => '0',
                    'width' => '200',
                    'height' => '100',
                    'reason'=>$text,
                    'jenis_response'=>'BASE64'),
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
                Certificate::where('diklat_id',$diklat_id)->where('diklat_detail_id',$diklat_detail_id)->where('user_id',$sertif->user_id)->update(['id_dokumen_transkip'=>$id_dokumen]);
                
                
                $this->download_doc_transkip($id_dokumen);
            }else{
                ErrorDc::create(['response'=>$response]);
                return response()->json(['error'=>$response]);

            }

        }
        
    }

    public function download_doc($id){
         $curl = curl_init();
    $get_id = Certificate::where('id_dokumen',$id)->first();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://10.2.237.166/api/sign/download/".$id,
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

           ErrorDc::create(['response'=>$response]);
           return response()->json(['error'=>$response]);

        }
    }
    }
      public function download_doc_transkip($id){
         $curl = curl_init();
    $get_id = Certificate::where('id_dokumen_transkip',$id)->first();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://10.2.237.166/api/sign/download/".$id,
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
    
    $dir = public_path('digital-signatures-transkip');
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


    
}

public function download_sign($id){

    // "http://10.2.237.166/api/sign/download/"

    $curl = curl_init();
    $get_id = Certificate::where('id_dokumen',$id)->first();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://10.2.237.166/api/sign/download/".$id,
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
                // $put_file =  readfile($response);
                // file_put_contents($file_name,$put_file);
                header('Cache-Control: public');
                header('Content-type: application/pdf');
                header("Content-Transfer-Encoding: binary");
                header('Content-Disposition: attachment; filename="' . $get_id->id . '.pdf"');
                header('Content-Length: ' . strlen($response));
                // echo $response;
                 
                curl_close($curl);

        }else{
            return $response;
        }
    }
}

public function verif(){

$url_signed = 'http://10.2.237.166/api/sign/verify';

                    //passphrase for the certificate
                    
                        
                    //curl initialization
                    $curl = curl_init();

                    $asal = public_path('testing.pdf');

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => $url_signed,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => array('signed_file'=> new \CURLFILE($asal,'application/pdf')),
                      CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic ZXNpZ246cXdlcnR5",
                        
                      ),
                    ));

                    $response = curl_exec($curl);
                    //Fungsi untuk mengambil nilai http request
                    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
                    //===
                    curl_close($curl);

                    $result = json_decode($response);


print_r($response);

}

public function registrasi(){

}


public function prosesds(){
    $get_cs = CertificateSetting::where('id',1)->first();
    $passphrase = 'Eskopi1234&&';
    $text  = $get_cs->reason;

    $sertifikat = Certificate::where('status',3)->get();
if($sertifikat){
foreach($sertifikat as $sertif) {
        $this->downloadCertificate($sertif->id);
        $this->upload_doc($sertif->diklat_id,$sertif->diklat_detail_id,$sertif->user_id,$text,$passphrase);
        $this->upload_doc_transkip($sertif->diklat_id,$sertif->diklat_detail_id,$sertif->user_id,$text,$passphrase);
        $sertif = Certificate::where('diklat_id',$sertif->diklat_id)->where('diklat_detail_id',$sertif->diklat_detail_id)->where('user_id',$sertif->user_id)->first();

        $path = public_path('sertif/');        

        $user = User::find($sertif->user_id);
        $asal = $path.$sertif->id.'.pdf';
        // if(isset($asal)){
            $url = 'http://10.2.237.166/api/sign/pdf';

            $url_signed = 'http://10.2.237.166/api/sign/pdf';

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
                CURLOPT_POSTFIELDS => array('file'=> new \CURLFILE($asal,'application/pdf'),
                    'nik' => '3174047004710002',
                    'passphrase' => $passphrase,
                    'tampilan' => 'invisible',
                    'page' => '1',
                    'image' => 'false',
                    'linkQR' => 'https://google.com',
                    'xAxis' => '0',
                    'yAxis' => '0',
                    'width' => '200',
                    'height' => '100',
                    'reason'=>$text,
                    'jenis_response'=>'BASE64'),
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
                Certificate::where('diklat_id',$sertif->diklat_id)->where('diklat_detail_id',$sertif->diklat_detail_id)->where('user_id',$sertif->user_id)->update(['id_dokumen'=>$id_dokumen]);
                $diklat_jos = Diklat::where('id',$sertif->diklat_id)->first();
                $this->download_doc($id_dokumen);

                Mail::to($user->email)->send(new EmailLulus($diklat_jos->title,$sertif->nilai));

            }else{

                ErrorDc::create(['response'=>$response]);
                return response()->json(['error'=>$response]);




            }

        // }


    }



    if(!empty($httpCode)){
        if($httpCode !== 200){


        ErrorDc::create(['response'=>$response]);
        return response()->json(['error'=>$response]);



    }else{

        Certificate::where('diklat_id',$sertif->diklat_id)->where('diklat_detail_id',$sertif->diklat_detail_id)->update(array('status' => '1'));
        return response()->json(['success'=>'Sertifikat Berhasil Disetujui']);    


    }
        }else{
            return "tidak ada peserta yang harus di setujui";
    }
    
}else{
    return "tidak ada peserta yang harus di setujui";
}
    








}





}


