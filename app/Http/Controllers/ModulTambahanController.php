<?php

namespace App\Http\Controllers;


use App\ModulTambahan;
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
use Carbon\Carbon;
use App\ExerciseDetail;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Http\File;
// use Illuminate\Support\Facades\Storage;

class ModulTambahanController extends VController
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

    public function index()
    {
        return "coba";
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
        $mata_diklat_id = $request->get('mt_id');
        $get_id =  ModulTambahan::where('id',$id)->first(); 
        
        return view('vendor.voyager.modul-tambahan.read',compact('get_id'));

        
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

    public function edit($id)
    {
        
        
        $get_id =  ModulTambahan::find($id); 


        return view('vendor.voyager.modul-tambahan.edit',compact('get_id'));
        
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        
        $data =  ModulTambahan::find($id)->update([
            'judul' => $request->judul,
            'link' => $request->link,
            'mata_diklat_id' => $request->mata_diklat_id,
            'section_id' => $request->section_id,

        ]);
        
        return redirect()->route("voyager.mata-diklats.edit", $request->mata_diklat_id)
            ->with([
                'message'    => __('voyager::generic.successfully_updated')." Modul Tambahan",
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
        $mata_diklat_id = $request->get('mt_id');
        $section_id = $request->get('st_id');
        // Find form by slug
        $get_id =  ModulTambahan::where('mata_diklat_id',$mata_diklat_id)->first(); 


        return view('vendor.voyager.modul-tambahan.add',compact('mata_diklat_id','section_id','get_id'));


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
        // $slug = $this->getSlug($request);
        // $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // // Check permission
        // $this->authorize('add', app($dataType->model_name));
        // $model = app($dataType->model_name);

        // // Validate fields with ajax
        // $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        // // $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());
        // $insert = $request->except('_token', 'options');
        // $insert['options'] = $request->settings !== 'manual' ? json_encode($request->options) : null;
        // $insert['line'] = (Exercise::whereMataDiklatId($request->mata_diklat_id)
        //     ->whereSectionId($request->section_id)->count() + 1);
        // $data = Exercise::create($insert);
        // if ($request->settings !== 'manual') {
        //     $listSoal = $data->autoChose($request->options, $data->mata_diklat_id);

        //     foreach ($listSoal as $kls => $vls) {
        //         foreach ($vls as $klsd => $vlsd) {
        //             ExerciseDetail::create([
        //                 'exercise_id' => $data['id'],
        //                 'key' => 'soal',
        //                 'value' => $vlsd['soal'],
        //                 'details' => $vlsd['details'],
        //                 'bank_soal_id' => $vlsd['id']
        //             ]);
        //         }
        //     }
        // }

        // event(new BreadDataAdded($dataType, $data));

        // return redirect()
        // ->route("voyager.mata-diklats.edit", $request->mata_diklat_id)
        // ->with([
        //         'message'    => __('voyager::generic.successfully_added_new')." {$dataType->display_name_singular}",
        //         'alert-type' => 'success',
        //     ]);
        // $get_id =  ModulTambahan::where('mata_diklat_id',$request->mata_diklat_id)->first(); 

        // Variable to check
        // $url = $request->link;

        // // Remove all illegal characters from a url
        // $url = filter_var($url, FILTER_SANITIZE_URL);

        // // Validate url
        // if (!filter_var($url, FILTER_VALIDATE_URL)) {
        //     return redirect()->route("voyager.mata-diklats.edit", $request->mata_diklat_id)
        //     ->with([
        //         'message'    => "Link tidak valid",
        //         'alert-type' => 'danger',
        //     ]);
        // } 
        
        $data =  ModulTambahan::create([
            'judul' => $request->judul,
            'link' => $request->link,
            'mata_diklat_id' => $request->mata_diklat_id,
            'section_id' => $request->section_id,

        ]);
        
        return redirect()->route("voyager.mata-diklats.edit", $request->mata_diklat_id)
            ->with([
                'message'    => __('voyager::generic.successfully_updated')." Modul Tambahan",
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
        

        $data = [
            'message'    => __('voyager::generic.successfully_deleted')." ",
            'alert-type' => 'success',
        ];
       
        $delete = ModulTambahan::find($id);

        $delete->delete();
        

        return redirect()
            ->route("voyager.mata-diklats.edit", $request->get('mt_id'))
            ->with($data);
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

    public function addBankSoal(Request $request, $id)
    {
        $bankSoal = BankSoal::find($request->bank_soal_id);
        $pretest = Exercise::find($id);
        ExerciseDetail::create([
            'exercise_id' => $pretest->id,
            'type' => $bankSoal->type_soal,
            'key' => 'soal',
            'value' => $bankSoal->soal,
            'details' => $bankSoal->details,
            'bank_soal_id' => $bankSoal->id,
        ]);

        return redirect()
            ->back()
            ->with([
                'message'    => __('Bank soal berhasil di tambahkan!'),
                'alert-type' => 'success',
            ]);
    }

    public function removeBankSoal(Request $request, $id)
    {
        $pretestDetailIds = ExerciseDetail::whereBankSoalId($request->bank_soal_id)->pluck('id')->toArray();
        ExerciseDetail::destroy($pretestDetailIds);

        return redirect()
            ->back()
            ->with([
                'message'    => __('Bank soal berhasil di hapus!'),
                'alert-type' => 'error',
            ]);
    }

    public function waktu(){
        return view('vendor.voyager.modul-tambahan.waktu');
    }
}
