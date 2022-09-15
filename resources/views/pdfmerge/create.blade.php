@extends ('layouts.backend')
@section('title',' Merge PDF ')
@section('heading','Choose files to merge')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Merge PDF</strong></li>
@endsection

@section('content')
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                Please choose multiple PDF files.

            </h3>

        </div>
        <!-- /.box-header -->
        <div class="box-body">


                {!! Form::open(['route' => 'dinesh_shrestha_pdf_merge.store','method'=>'post','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}

                <input type="file" name="file[]" id="file" multiple>

            PHP


            @if (count($errors) > 0)
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form action="/upload" method="post" enctype="multipart/form-data">
                @if (count($errors) > 0)
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            <br>
                <input type='submit' name='submit' value='Upload'>
            </form>

            <div class="box-footer">




            </div>

        </div>
    </div>



@endsection