@inject('request', 'Illuminate\Http\Request')

<div class="container-fluid">

	<!-- Language changer -->
	<div class="row">
		<div class="col-md-6">
			<div class="pull-left mt-10">
		        <select class="form-control input-sm" id="change_lang">
		            @foreach(config('constants.langs') as $key => $val)
		                <option value="{{$key}}" 
		                	@if( (empty(request()->lang) && config('app.locale') == $key) 
		                	|| request()->lang == $key) 
		                		selected 
		                	@endif
		                >
		                	{{$val['full_name']}}
		                </option>
		            @endforeach
		        </select>
	    	</div>
		</div>
		<div class="col-md-6">
			<div class="pull-right">
	        	@if(!($request->segment(1) == 'business' && $request->segment(2) == 'register'))

	        		<!-- Register Url -->
		        	@if(env('ALLOW_REGISTRATION', true))
		            	<a 
		            		href="{{ route('business.getRegister') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif"
		            		class="btn bg-maroon btn-flat margin" 
		            	><b>{{ __('business.not_yet_registered')}}</b> {{ __('business.register_now') }}</a>

		            	<!-- pricing url -->
			            @if(Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) != 'pricing')
		                	<a href="{{ action('\Modules\Superadmin\Http\Controllers\PricingController@index') }}">@lang('superadmin::lang.pricing')</a>
		            	@endif
		            @endif
		        @endif

		        @if(!($request->segment(1) == 'business' && $request->segment(2) == 'register') && $request->segment(1) != 'login')
		        	{{ __('business.already_registered')}} <a href="{{ action('Auth\LoginController@login') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif">{{ __('business.sign_in') }}</a>
		        @endif
	        </div>
		</div>
	</div>

	<div class="row text-center">
		@if(file_exists(public_path('uploads/logo.png')))
			<div class="col-xs-12">
				<img src="/uploads/logo.png" class="img-rounded" alt="Logo" width="150" style="margin-bottom: 30px;">
			</div>
		@else
	    	<h1 class="text-center page-header">{{ config('app.name', 'ultimatePOS') }}</h1>
	    @endif
	</div>
</div>