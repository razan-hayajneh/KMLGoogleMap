<!-- file_path Field -->
<div class="form-group col-sm-6">
    {!! Form::label('file_path', 'File Path :') !!}
    {!! Form::file('file_path', null, ['class' => 'form-control', 'required','accept'=>'.kml']) !!}
</div>

