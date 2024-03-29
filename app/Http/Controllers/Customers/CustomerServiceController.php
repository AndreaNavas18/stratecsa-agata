<?php

namespace App\Http\Controllers\Customers;

use App\Exports\CustomerServices\CustomerServicesExport;
use App\Exports\Services\ServicesExport;
use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use App\Models\Customers\CustomerService;
use App\Models\Employees\Employee;
use App\Models\General\City;
use App\Models\General\Country;
use App\Models\General\Department;
use App\Models\General\Service;
use App\Models\Providers\Provider;
use App\Models\Tickets\Ticket;
use App\Models\General\Proyecto;
use App\Models\Customers\CustomerServiceFile;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CustomerServiceImport;
use Illuminate\Support\Facades\Log;
use App\Models\Helpers;



class CustomerServiceController extends Controller
{

    public function indexAll() {
        session::flash('tab','servicesAll');


        $user = Auth::user();
        $allowedRoles = [2, 3, 7, 8];

        if (in_array(Auth()->user()->role_id, $allowedRoles) && $user->customer_id) {
            // Filtra los servicios por el customer_id del usuario autenticado
            $customerServices = CustomerService::where('customer_id', $user->customer_id)
                ->orderBy('created_at', 'DESC')
                ->paginate();
            $customers = Customer::where('id', $user->customer_id)->get(); // Si el cliente está asociado al usuario autenticado, puede obtenerlo directamente desde el usuario
            $customer = Customer::with('customerContacs', 'customerServices')->findOrFail($user->customer_id); // También puedes usar $user->customer_id aquí
            $departments = Department::get();
            $cities = City::get();
            $countries = Country::get();
            $servicesList = Service::get();
            $typesServices = Service::get();
            // Obtener los ids únicos de los proyectos asociados a los servicios del cliente
            $projectIds = $customerServices->pluck('proyecto_id')->unique();
            $proyectos = Proyecto::whereIn('id', $projectIds)->get();

            $tabPanel='customerServicesTabEdit';
            $typesInstalations = [
                'Propia'    =>'Propia',
                'Terceros'  =>'Terceros'
            ];
            $providers = Provider::get();
            $camposAdicionales = [
                'ip',
                'vlan',
                'mascara',
                'gateway',
                'mac',
                'ancho_de_banda',
                'ip_vpn',
                'tipo_vpn',
                'user_vpn',
                'password_vpn',
                'user_tunel',
                'id_tunel',
                'tecnologia',
                'equipo',
                'modelo',
                'serial',
                'activo_fijo'
            ];


            return view('modules.customers.services.index', compact(
                'customers',
                'countries',
                'departments',
                'cities',
                'tabPanel',
                'customerServices',
                'servicesList',
                'typesServices',
                'proyectos',
                'typesInstalations',
                'providers',
                'camposAdicionales'
            ));
        } else {
            
            $servicesList=Service::get();
            $customers= Customer::with('customerContacs','customerServices')->get();
            $countries= Country::get();
            $departments= Department::get();
            $cities=City::get();
            $customerServices= CustomerService::orderBy('created_at', 'DESC')->paginate();
            $typesInstalations = [
                'Propia'    =>'Propia',
                'Terceros'  =>'Terceros'
            ];
            $providers = Provider::get();
            $tabPanel='customerServicesTabEdit';
            $typesServices=Service::get();
            $proyectos = Proyecto::get();
            $camposAdicionales = [
                'ip',
                'vlan',
                'mascara',
                'gateway',
                'mac',
                'ancho_de_banda',
                'ip_vpn',
                'tipo_vpn',
                'user_vpn',
                'password_vpn',
                'user_tunel',
                'id_tunel',
                'tecnologia',
                'equipo',
                'modelo',
                'serial',
                'activo_fijo'
            ];

            return view('modules.customers.services.index', compact(
                'customers',
                'countries',
                'departments',
                'cities',
                'tabPanel',
                'customerServices',
                'servicesList',
                'typesInstalations',
                'providers',
                'typesServices',
                'proyectos',
                'camposAdicionales'
            ));

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index($customerId)
    {
            session::flash('tab','servicesIndex');
            $servicesList=Service::get();
            $customers= Customer::with('customerContacs','customerServices')->get();
            $customer= Customer::with('customerContacs','customerServices')->findOrFail($customerId);
            $departments= Department::get();
            $cities=City::get();
            $countries= Country::get();
            $customerServices= CustomerService::customerId($customerId)->paginate();
            $typesInstalations = [
                'Propia'    =>'Propia',
                'Terceros'  =>'Terceros'
            ];
            $providers = Provider::get();
            $tabPanel='customerServicesTabEdit';
            $typesServices=Service::get();
            $proyectos = Proyecto::get();
            $camposAdicionales = [
                'ip',
                'vlan',
                'mascara',
                'gateway',
                'mac',
                'ancho_de_banda',
                'ip_vpn',
                'tipo_vpn',
                'user_vpn',
                'password_vpn',
                'user_tunel',
                'id_tunel',
                'tecnologia',
                'equipo',
                'modelo',
                'serial',
                'activo_fijo'
            ];
    
            return view('modules.customers.edit', compact(
                'customer',
                'departments',
                'cities',
                'tabPanel',
                'customerServices',
                'customers',
                'servicesList',
                'typesInstalations',
                'providers',
                'typesServices',
                'countries',
                'proyectos',
                'camposAdicionales'
            ));

    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function indexSearch(Request $request,$customerId)
    {
        Log::info("Hice esto ");
        session::flash('tab','servicesindexSearch');
        $servicesList=Service::get();
        $customer= Customer::with('customerContacs','customerServices')->findOrFail($customerId);
        $departments= Department::get();
        $cities=City::get();
        $typesInstalations = [
            'Propia'    =>'Propia',
            'Terceros'  =>'Terceros'
        ];
        $providers = Provider::get();
        $tabPanel='customerServicesTabEdit';
        $typesServices=Service::get();
        $data=$request->all();
        $countries= Country::get();
        $proyectos= Proyecto::get();
        $customerServices= CustomerService::buscar($data,$customerId,'customer');
        if ($request->action=='buscar') {
            $customerServices = $customerServices->paginate();
            return view('modules.customers.edit', compact(
                'customer',
                'departments',
                'cities',
                'tabPanel',
                'customerServices',
                'servicesList',
                'typesInstalations',
                'providers',
                'data',
                'typesServices',
                'countries',
                'proyectos'
            ));
        } else {
            $customerServices = $customerServices->get();
            return (new CustomerServicesExport($customerServices))->download('Clientes_servicios.xlsx');
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request ,$customerId=null)
    {
        DB::beginTransaction();
        $customerService                            = new CustomerService();
        $customerService->stratecsa_id              = $request->stratecsa_id;
        $customerService->otp                       = $request->otp;
        $customerService->id_serviciocliente        = $request->id_serviciocliente;
        $customerService->service_id                = $request->service_id;
        $customerService->city_id                   = $request->city_id;
        $customerService->date_service              = $request->date_service;
        $customerService->latitude_coordinates      = $request->latitude_coordinates;
        $customerService->longitude_coordinates     = $request->longitude_coordinates;
        $customerService->installation_type         = $request->installation_type;
        $customerService->country_id                = $request->country_id;
        $customerService->department_id             = $request->department_id;

          
        // Verificar si el campo proyecto_id está presente y tiene un valor válido
        // if($request->has('proyecto_id')){
        //     if (is_numeric($request->proyecto_id)) {
        //         $customerService->proyecto_id = $request->proyecto_id;
        //     }else if ($request->proyecto_id === '') {
        //         $customerService->proyecto_id = null;
        //     }
        // }
        if ($request->filled('proyecto_id') && is_numeric($request->proyecto_id)) {
            $customerService->proyecto_id = $request->proyecto_id;
        }

        if ($request->filled('provider_id')) {
            $customerService->provider_id           = $request->provider_id;
        }
        if (is_null($customerId)) {
            $customerService->customer_id           = $request->customer_id;
        } else {
            $customerService->customer_id           = $customerId;
        }
        $customerService->state                     = 'Activo';
        $customerService->description               = $request->description;

        if($request->filled('ip')) {
            $customerService->ip                        = $request->ip;
        }
        if($request->filled('vlan')) {
            $customerService->vlan                      = $request->vlan;
        }
        if($request->filled('mascara')) {
            $customerService->mascara                   = $request->mascara;
        }
        if($request->filled('gateway')) {
            $customerService->gateway                   = $request->gateway;
        }
        if($request->filled('mac')) {
            $customerService->mac                       = $request->mac;
        }
        if($request->filled('ancho_de_banda')) {
            $customerService->ancho_de_banda            = $request->ancho_de_banda;
        }
        if($request->filled('ip_vpn')) {
            $customerService->ip_vpn                    = $request->ip_vpn;
        }
        if($request->filled('tipo_vpn')) {
            $customerService->tipo_vpn                  = $request->tipo_vpn;
        }
        if($request->filled('user_vpn')) {
            $customerService->user_vpn                  = $request->user_vpn;
        }
        if($request->filled('password_vpn')) {
            $customerService->password_vpn              = $request->password_vpn;
        }
        if($request->filled('user_tunel')) {
            $customerService->user_tunel                = $request->user_tunel;
        }
        if($request->filled('id_tunel')) {
            $customerService->id_tunel                  = $request->id_tunel;
        }
        if($request->filled('tecnologia')) {
            $customerService->tecnologia                = $request->tecnologia;
        }
        if($request->filled('equipo')) {
            $customerService->equipo                    = $request->equipo;
        }
        if($request->filled('modelo')) {
            $customerService->modelo                    = $request->modelo;
        }
        if($request->filled('serial')) {
            $customerService->serial                    = $request->serial;
        }
        if($request->filled('activo_fijo')) {
            $customerService->activo_fijo               = $request->activo_fijo;
        }

        if (!$customerService->save()) {
            DB::rollBack();
            Alert::error('Error', 'Error al insertar registro.');
            return redirect()->back();
        }

         //guardar documentos
         if ($request->hasFile('files')) {
            Log::info("si hay archivos en el servicio");
            $files = $request->file('files');
            foreach ($files as $value) {
                $fileService= new CustomerServiceFile();
                // Obtener el archivo
                $file = $value;
                Log::info($file);
                // Obtener el nombre original del archivo
                $nameOriginal = $file->getClientOriginalName();
                // Carpeta de destino
                $destinationPath = public_path('storage/servicios/documentos');
                // Generar un nombre de archivo único
                $slugArchivo = 'documento_' . $customerService->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                // Mover el archivo a la carpeta de destino
                $file->move($destinationPath, $slugArchivo);
                // Definir la ruta del archivo
                $path = 'servicios/documentos/' . $slugArchivo;
                $fileService->name_original = strtolower($nameOriginal).'_'.uniqid();
                $fileService->slug = strtolower($slugArchivo);
                $fileService->path = $path;
                $fileService->customers_services_id = $customerService->id;

                if(!$fileService->save()) {
                    Log::info("No se guardo el archivo");
                    DB::rollBack();
                    Alert::error('Error', 'Error al insertar el registro.');
                    return redirect()->back();
                }
            }
        }else {
            Log::info("no hay archivos en el servicio");
        }
      
        DB::commit();
        Alert::success('Bien hecho!', 'Registro insertado correctamente');
        return redirect()->back();
    }


     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        session::flash('tab','servicesShow');
        $customer= Customer::findOrFail($id);
        $customerServices= CustomerService::customerId($id)->paginate();
        $customerServicesFiles = CustomerServiceFile::where('customers_services_id', $id)->get();
        $tabPanel='customerServicesTabShow';
        $providers= Provider::get();
        $typesInstalations=['Propia','Terceros'];
        $departments= Department::get();
        $typesServices=Service::get();
        $countries= Country::get();
        $proyectos= Proyecto::get();
        $customers = Customer::get();
        return view('modules.customers.show', compact(
            'customer',
            'customerServices',
            'tabPanel',
            'providers',
            'typesInstalations',
            'departments',
            'typesServices',
            'countries',
            'proyectos',
            'customers',
            'customerServicesFiles',
        ));
    }

    public function showService($id) {
        session::flash('tab','service');
        $service= CustomerService::findOrFail($id);
        $customerServicesFiles = CustomerServiceFile::where('customers_services_id', $id)->get();

        return view('modules.customers.services.show', compact(
            'service',
            'customerServicesFiles'
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showSearch(Request $request,$customerId)
    {
       
        session::flash('tab','servicesshowSearch');
        $customer= Customer::findOrFail($customerId);
        $tabPanel='customerServicesTabShow';
        $providers= Provider::get();
        $proyectos= Proyecto::get();
        $typesInstalations=['Propia','Terceros'];
        $departments= Department::get();
        $typesServices=Service::get();
        $data=$request->all();
        $countries= Country::get();
        $customerServices= CustomerService::buscar($data,$customerId,'customer');
        if ($request->action=='buscar') {
            $customerServices = $customerServices->paginate();
            return view('modules.customers.show', compact(
                'customer',
                'customerServices',
                'tabPanel',
                'providers',
                'typesInstalations',
                'departments',
                'typesServices',
                'data',
                'countries',
                'proyectos'
            ));
        } else {
            $customerServices = $customerServices->get();
            return (new CustomerServicesExport($customerServices))->download('Clientes_servicios.xlsx');
        }
    }

    public function edit($id) {

        $service= CustomerService::findOrFail($id);
        $servicesList=Service::get();
        $customers= Customer::get();
        $customersList= Customer::get();
        $countries= Country::get();
        $departments= Department::get();
        $cities=City::get();
        //Quiero traer el tipo de instalacion que tiene el servicio del id que estoy pasando
        $typesInstalations = [
            'Propia'    =>'Propia',
            'Terceros'  =>'Terceros'
        ];
        $typeInstalation = $service->installation_type;
        Log::info($typeInstalation);
        $serviceDescription = $service->description;
        $providers = Provider::get();
        $tabPanel='customerServicesTabEdit';
        $typesServices=Service::get();
        $proyectos = Proyecto::get();
        $camposAdicionales = [
            'ip',
            'vlan',
            'mascara',
            'gateway',
            'mac',
            'ancho_de_banda',
            'ip_vpn',
            'tipo_vpn',
            'user_vpn',
            'password_vpn',
            'user_tunel',
            'id_tunel',
            'tecnologia',
            'equipo',
            'modelo',
            'serial',
            'activo_fijo'
        ];
       
        return view(' modules.customers.services.partials.edit.serviceEdit', compact(
            'service',
            'customers',
            'countries',
            'departments',
            'cities',
            'tabPanel',
            'servicesList',
            'typesInstalations',
            'providers',
            'typesServices',
            'proyectos',
            'camposAdicionales',
            'customersList',
            'typeInstalation',
            'serviceDescription'
        ));
    }


    public function updateService(Request $request, $id) {

        DB::beginTransaction();
        $customerService                            = CustomerService::findOrFail($id);
        $customerService->stratecsa_id              = $request->stratecsa_id;
        $customerService->id_serviciocliente        = $request->id_serviciocliente;
        $customerService->otp                       = $request->otp;
        $customerService->customer_id               = $request->customer_id;
        $customerService->proyecto_id               = $request->proyecto_id;
        $customerService->service_id                = $request->service_id;
        $customerService->city_id                   = $request->city_id;
        $customerService->date_service              = $request->date_service;
        $customerService->latitude_coordinates      = $request->latitude_coordinates;
        $customerService->longitude_coordinates     = $request->longitude_coordinates;
        $customerService->installation_type         = $request->installation_type;
        $customerService->state                     = $request->state;
        $customerService->description               = $request->description;

         // Verificar si country_id está presente en la solicitud
         if (!$request->has('country_id')) {
            $customerService->country_id = $customerService->country_id;
        }

        
        if (!$customerService->save()) {
            DB::rollBack();
            Alert::error('Error', 'Error al actualizar registro.');
            return redirect()->back();
        }

        
        DB::commit();
        Alert::success('Bien hecho!', 'Registro actualizado correctamente');
        return redirect()->back();

    }

    public function editConfig($id) {
        session::flash('tab','config');

        $service= CustomerService::findOrFail($id);
        $customerServicesFiles = CustomerServiceFile::where('customers_services_id', $id)->get();
        $anchosDeBanda = [
            '1'  =>   'Carga',
            '2'  =>  'Descarga'
        ];
        $anchoBanda = $service->ancho_de_banda;
        Log::info($anchoBanda);
        $tecnologias = [
            '1'     =>  'Radio',
            '2'     =>  'Fibra',
            '3'     =>  'Satelital'
        ];
        $tecnologia = $service->tecnologia;
        Log::info($tecnologia);

        return view('modules.customers.services.partials.edit.configServiceEdit', compact(
            'service',
            'customerServicesFiles',
            'anchosDeBanda',
            'anchoBanda',
            'tecnologias',
            'tecnologia'
        ));

    }

     public function updateConfig(Request $request, $id) {

        DB::beginTransaction();
        $customerService                            = CustomerService::findOrFail($id);
        $customerService->ip                        = $request->ip;
        $customerService->vlan                      = $request->vlan;
        $customerService->mascara                   = $request->mascara;
        $customerService->gateway                   = $request->gateway;
        $customerService->mac                       = $request->mac;
        $customerService->ancho_de_banda            = $request->ancho_de_banda;
        $customerService->ip_vpn                    = $request->ip_vpn;
        $customerService->tipo_vpn                  = $request->tipo_vpn;
        $customerService->user_vpn                  = $request->user_vpn;
        $customerService->password_vpn              = $request->password_vpn;
        $customerService->user_tunel                = $request->user_tunel;
        $customerService->id_tunel                  = $request->id_tunel;
        $customerService->tecnologia                = $request->tecnologia;
        $customerService->equipo                    = $request->equipo;
        $customerService->modelo                    = $request->modelo;
        $customerService->serial                    = $request->serial;
        $customerService->activo_fijo               = $request->activo_fijo;

        if (!$customerService->save()) {
            DB::rollBack();
            Alert::error('Error', 'Error al actualizar registro.');
            return redirect()->back();
        }

         //guardar documentos
         if ($request->hasFile('files')) {
            Log::info("si hay archivos en el servicio");
            $files = $request->file('files');
            foreach ($files as $value) {
                $fileService= new CustomerServiceFile();
                // Obtener el archivo
                $file = $value;
                Log::info($file);
                // Obtener el nombre original del archivo
                $nameOriginal = $file->getClientOriginalName();
                // Carpeta de destino
                $destinationPath = public_path('storage/servicios/documentos');
                // Generar un nombre de archivo único
                $slugArchivo = 'documento_' . $customerService->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                // Mover el archivo a la carpeta de destino
                $file->move($destinationPath, $slugArchivo);
                // Definir la ruta del archivo
                $path = 'servicios/documentos/' . $slugArchivo;
                $fileService->name_original = strtolower($nameOriginal).'_'.uniqid();
                $fileService->slug = strtolower($slugArchivo);
                $fileService->path = $path;
                $fileService->customers_services_id = $customerService->id;

                if(!$fileService->save()) {
                    Log::info("No se guardo el archivo");
                    DB::rollBack();
                    Alert::error('Error', 'Error al insertar el registro.');
                    return redirect()->back();
                }
            }
        }else {
            Log::info("no hay archivos en el servicio");
        }

        DB::commit();
        Alert::success('Bien hecho!', 'Registro actualizado correctamente');
        return redirect()->back();
        
     }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $customerService                            = CustomerService::findOrFail($id);
        $customerService->stratecsa_id              = $request->stratecsa_id;
        $customerService->service_id                = $request->service_id;
        $customerService->city_id                   = $request->city_id;
        $customerService->date_service              = $request->date_service;
        $customerService->latitude_coordinates      = $request->latitude_coordinates;
        $customerService->longitude_coordinates     = $request->longitude_coordinates;
        $customerService->installation_type         = $request->installation_type;
        $customerService->country_id                = $request->country_id;
        $customerService->proyecto_id               = $request->proyecto_id;
        if ($request->filled('provider_id')) {
            $customerService->provider_id           = $request->provider_id;
        }
        $customerService->description               = $request->description;
        if (!$customerService->save()) {
            DB::rollBack();
            Alert::error('Error', 'Error al actualizar registro.');
            return redirect()->back();
        }

        DB::commit();
        Alert::success('Bien hecho!', 'Registro actualizado correctamente');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            if (!CustomerService::findOrFail($id)->delete()) {
                Alert::error('Error', 'Error al eliminar registro.');
                return redirect()->back();
            }
            DB::commit();
            Alert::success('¡Éxito!', 'Registro eliminado correctamente');
            return redirect()->back();
        } catch (QueryException $th) {
            if ($th->getCode() === '23000') {
                Alert::error('Error!', 'No se puede eliminar el registro porque está asociado con otro registro.');
                return redirect()->back();
            } else {
                Alert::error('Error!', $th->getMessage());
                return redirect()->back();
            }
        }
    }

    public function import(Request $request)
    {
        if($request->hasFile('customerservicesdocumento')){

            $file = $request->file('customerservicesdocumento');
            Excel::import(new CustomerServiceImport, $file);
            Log::info("funcione, importe los servicios");

            Alert::success('Bien hecho!', 'Servicio(s) importado(s) correctamente');
            return redirect()->back();

            
        }else {
            Alert::error('Error', 'Error al importar archivo.');
            Log::info("no funcioneeee");
            return redirect()->back();
        }
    }

    public function showConfig($id) {
        session::flash('tab','config');

        $service= CustomerService::findOrFail($id);
        $customerServicesFiles = CustomerServiceFile::where('customers_services_id', $id)->get();


        return view('modules.customers.services.partials.show.configService', compact(
            'service',
            'customerServicesFiles'
        ));
    }
}