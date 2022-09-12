<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/test/mails', 'UserController@mailTest');

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

Route::get('pijar', 'Frontend\BlogController@index');
Route::get('link', 'Frontend\BlogController@link');
Route::get('unit-kerja', 'Frontend\BlogController@unitkerja');
Route::get('pijar/{slug}', 'Frontend\BlogController@detail');
Route::get('prosesds', 'DigitalSignatureController@prosesds');
Route::get('delWistyaHashedId/{id}', 'Frontend\MyCourseController@delWistyaHashedId');
Route::get('createzoom/{topic}/{password}', 'Frontend\ZoomController@createZoomRoom');
Route::get('deletewistia/{id}', 'Frontend\MyCourseController@deletewistia');
Route::get('certificates/{certificate}/check-transkip', 'CertificateController@generateTranskipNilaiCek')
            ->name('check-transkip');
            
Route::group(['middleware' => 'monitor_log'], function () {
    Auth::routes();

    Route::get('/', 'HomeController@index')->name('home');
    Route::group(['namespace' => 'Frontend'], function () {
        Route::resource('diklat', 'DiklatsController');
        Route::resource('pages', 'PageController');
        Route::get('check-certificate/{no}', 'PageController@checkCertificate')->name('check-certificate');
        Route::post('find-locations', 'DiklatsController@findLocation');
        Route::post('check-nip', 'DiklatsController@checkNip');
        Route::group(['middleware' => 'auth'], function () {
            Route::resource('my-course', 'MyCourseController');
            // Route::get('my-course/{diklat_id}', 'MyCourseController@show')->name('showya');
            
            Route::group(['prefix' => 'my-course'], function () {
                Route::post('upload-persyaratn/{id}', 'MyCourseController@uploadPersyaratn');
                Route::get('{diklat_id}/{mata_diklat_id}/{type}/{sction_id}/{id}', 'MyCourseController@showMaterial')->name('showMaterial');
                Route::post('answer-quizz', 'MyCourseController@answerQuiz')->name('answer.quizz');
                Route::post('answer-exercise/{id}', 'MyCourseController@answerExercise')->name('answer.exercise');
                Route::post('answer-encounter/{id}', 'MyCourseController@answerEncounter')->name('answer.encounter');
                Route::post('answer-pre-post/{type}/{id}/{id_detail}', 'MyCourseController@answerPrePro')->name('answer.prepost');
                Route::post('answer-survey/{id}', 'MyCourseController@answerSurvey')->name('answer.survey');
                 Route::post('answer-survey-instruktur/{id}', 'MyCourseController@answerSurveyInstruktur')->name('answer.survey_instruktur');
                Route::get('join-virtual-class/{id}', 'MyCourseController@joinVirtualClass')->name('join.virtual-class');
                Route::post('show-certificate/{diklat}/{user}', 'MyCourseController@showCertificate')->name('show.certificate');
                Route::post('show-transkip/{diklat}/{user}', 'MyCourseController@showTranskip')->name('show.transkip');
                Route::get('print-certificate', 'MyCourseController@printCertificate')->name('print.certificate');
                Route::post('upload-foto', 'MyCourseController@uploadFoto')->name('upload.foto');
            });
        });
    });


    Route::group(['prefix' => 'admin'], function () {
        Voyager::routes();
        // Report System
        
        Route::resource('reports', 'ReportController', [
            'names' => 'voyager.reports'
        ]);
        Route::resource('modul-tambahan', 'ModulTambahanController', [
             'names' => 'voyager.modul-tambahan'
         ]);
        // Route::resource('modul-tambahan', ModulTambahanController::class);
        Route::post('reports/excel/{excel}', [
            'uses' => 'ReportController@toExcel',
            'as' => 'voyager.reports.excel'
        ]);


        Route::post('digital-signatures/approved', 'DigitalSignatureController@approved', [
             'names' => 'digital-approved'
         ]);
        Route::post('digital-signatures/approved-peserta', 'DigitalSignatureController@approvedPeserta', [
             'names' => 'digital-approved-peserta'
         ]);
        Route::get('download_sign/{id}', 'DigitalSignatureController@download_sign')->name('download_sign');

        Route::get('verif', 'DigitalSignatureController@verif');
        Route::get('digital-signatures/registrasi', 'DigitalSignatureController@registrasi');

        Route::get('waktu', 'ModulTambahanController@waktu', [
             'names' => 'voyager.modul-tambahan'
         ]);


        // Diklat Details
        Route::post('mata-diklats/orders/{id}', [
            'uses' => 'MataDiklatController@order_item',
            'as' => 'voyager.mata-diklats.orders'
        ]);
        Route::post('diklats/bobots/{id}', [
            'uses' => 'DiklatController@bobots',
            'as' => 'voyager.diklats.bobots'
        ]);

        // Absen hadir
        Route::post('absen/hadir', [
            'uses' => 'VirtualClassController@hadirAbsen',
            'as' => 'voyager.absen.hadir'
        ]);

        Route::get('user-konfirm', [
            'uses' => 'UserController@getKonfirm',
            'as' => 'voyager.users.user-konfirm'
        ]);

        Route::get('user-document', [
            'uses' => 'UserController@getDocument',
            'as' => 'voyager.users.user-document'
        ]);

        Route::post('user-konfirm/{id}/{diklat_id}', [
            'uses' => 'UserController@postKonfirm',
            'as' => 'voyager.users.post-konfirm'
        ]);

        Route::post('user-document/{id}/{diklat_id}', [
            'uses' => 'UserController@postDocument',
            'as' => 'voyager.users.post-document'
        ]);

        Route::post('update-image/{id}', [
            'uses' => 'UserController@postDocument',
            'as' => 'voyager.users.update-image'
        ]);

        Route::get('get-user-assesment', [
            'uses' => 'EncouterDetailUserController@getReadyAssesment',
        ]);
        
        Route::get('get-remedial', [
            'uses' => 'EncouterDetailUserController@getRemedial',
        ]);
        
        

        Route::get('get-doesnt-done-assesment', [
            'uses' => 'EncouterDetailUserController@getNotReadyAssesment',
        ]);


        Route::get('send-encounter', [
            'uses' => 'EncouterDetailUserController@sendEncounter',
        ]);
        Route::get('send-remedial', [
            'uses' => 'EncouterDetailUserController@sendRemedial',
        ]);

        Route::get('get-start-assesment', [
            'uses' => 'EncouterDetailUserController@encounterAssesment',
        ]);

        Route::get('delete-assesment', [
            'uses' => 'EncouterDetailUserController@deleteAssesment',
        ]);

        Route::post('post-encounter-assesment', [
            'uses' => 'EncouterDetailUserController@postAssesment',
        ]);

        Route::get('get-user-bobot-assesment', [
            'uses' => 'EncouterDetailUserController@getReadyBobotAssesment',
        ]);

        Route::get('get-start-bobot-assesment', [
            'uses' => 'EncouterDetailUserController@encounterBobotAssesment',
        ]);

        Route::post('post-encounter-bobot-assesment', [
            'uses' => 'EncouterDetailUserController@postBobotAssesment',
        ]);

        Route::group(['namespace' => 'Frontend'], function () {
            Route::get('show-certificate/{diklat}/{user}', 'MyCourseController@showCertificate')
                ->name('admin.show.certificate');
        });

        // NOTE Modify semua jenis soal (Ujian, Latihan, Pretest, Postest)
        Route::resource('encouter-details', 'EncouterDetailController', [
            'names' => 'voyager.encouter-details'
        ]);

        Route::post('pretest-bank-soals/{id}', 'PretestController@addBankSoal')
            ->name('voyager.pretest-bank-soal.store');

        Route::delete('pretest-bank-soals/{id}', 'PretestController@removeBankSoal')
            ->name('voyager.pretest-bank-soal.delete');

        Route::post('postest-bank-soals/{id}', 'PostestController@addBankSoal')
            ->name('voyager.postest-bank-soal.store');

        Route::delete('postest-bank-soals/{id}', 'PostestController@removeBankSoal')
            ->name('voyager.postest-bank-soal.delete');

        Route::post('exercise-bank-soals/{id}', 'ExerciseController@addBankSoal')
            ->name('voyager.exercise-bank-soal.store');

        Route::delete('exercise-bank-soals/{id}', 'ExerciseController@removeBankSoal')
            ->name('voyager.exercise-bank-soal.delete');

        Route::post('encouter-bank-soals/{id}', 'EncouterController@addBankSoal')
            ->name('voyager.encouter-bank-soal.store');

        Route::delete('encouter-bank-soals/{id}', 'EncouterController@removeBankSoal')
            ->name('voyager.encouter-bank-soal.delete');

        Route::get('sampah/delete/{menu}/{id}', 'SampahController@deleteForce');
        Route::get('sampah/{menu}', 'SampahController@index')->name('voyager.sampah');
        Route::get('reports/pdf/{pdf}', 'ReportController@toPDF');
        Route::get('get-user-assesment-filter', 'EncouterDetailUserController@filter');
        Route::get('get-review-assesment', 'EncouterDetailUserController@review');

        Route::get('certificates/{certificate}/generate', 'CertificateController@generateCertificate')
            ->name('voyager.certificates.generate');
        Route::get('certificates/{certificate}/generate', 'CertificateController@generateCertificate')
            ->name('voyager.certificates.generate');
        Route::get('certificates/{certificate}/generatee', 'CertificateController@generateCertificatee')
            ->name('voyager.certificates.generate');
            Route::get('certificates/{certificate}/download', 'CertificateController@downloadCertificate')
            ->name('voyager.certificates.download');

        Route::get('downloadser/{certificate}', 'CertificateController@downloadser')
            ->name('voyager.certificates.serttt');




        Route::get('certificates/{certificate}/transkip-nilai', 'CertificateController@generateTranskipNilai')
            ->name('voyager.certificates.transkip');
        Route::get('certificates/{certificate}/transkip-nilai-admin', 'CertificateController@generateTranskipNilaiAdmin')
            ->name('voyager.certificates.transkipadmin');

        Route::post('certificates/generate-no-certificate', 'CertificateController@bulkUploadNoCertificate')
            ->name('voyager.certificates.generate-no-certificate');
        Route::post('certificates/senddc', 'CertificateController@sendDc')
            ->name('voyager.senddc');
        
        Route::get('get_notif/{diklat_id}/{detail_diklat_id}', 'CertificateController@getNotif')->name('get_notif');

    });


});
