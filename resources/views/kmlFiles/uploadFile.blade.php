<div class="content px-3">
    @include('adminlte-templates::common.errors')
    {!! Form::open(['route' => 'kmlFiles.store', 'files' => true, ' enctype' => 'multipart/form-data']) !!}
    <div class="card-body">
        <div class="row ">
            @include('kmlFiles.fields')
            {!! Form::submit('Save', ['class' => 'btn btn-primary ','style'=>'height:40px;margin:20px']) !!}
        </div>
    </div>
    {!! Form::close() !!}
</div>
