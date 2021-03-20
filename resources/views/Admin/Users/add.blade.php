@extends('Admin.Layout.app')
@section('title', 'Add'.' '.$label)
@section('content')

<section class="admin-content">
    <div class="bg-dark">
        <div class="container  m-b-30">
            <div class="row">
                <div class="col-12 text-white p-t-40 p-b-90">

                    <h4 class=""> Add {{$label}}</h4>

                </div>
            </div>
        </div>
    </div>

    <div class="container  pull-up">
        <div class="row">
            <div class="col-lg-12">

                <!--widget card begin-->
                <div class="card m-b-30">
                    <div class="card-body ">
                        <form action="{{url('admin/add-user')}}" method="post" id="add_edit_user">
                            @csrf
                            <input type="hidden" name="id" value="{{@$user['id']}}">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Full Name</label>
                                    <input type="text" class="form-control" name="full_name" id="full_name" placeholder="Full Name*" required value="{{old('full_name') ?: @$user->full_name}}">
                                    @if ($errors->has('full_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('full_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Refrence ID</label>
                                    <input type="text" class="form-control" name="refrence_id" id="refrence_id" placeholder="Refrence ID*" value="{{old('refrence_id') ?: @$user->refrence_id}}">
                                    @if ($errors->has('refrence_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('refrence_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Email</label>
                                    <input type="email" class="form-control" value="{{old('email') ?: @$user->email}}" name="email" id="email" placeholder="Email" required>
                                    @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Mobile Number</label>
                                    <input type="number" class="form-control" value="{{old('mobile_number') ?: @$user->mobile_number}}" name="mobile_number" id="mobile_number" placeholder="Mobile Number*" >
                                    @if ($errors->has('mobile_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mobile_number') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                            @if(!@$user->id)
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password*" required>
                                    @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password*" required>
                                    @if ($errors->has('password_confirmation'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="inputPassword4">Status</label>
                                    <select name="status" class="form-control js-select2" required>
                                        <option selected disabled>Select Status</option>
                                        <option value="Active" @if(@$user->status == 'Active') selected @endif>Active</option>
                                        <option value="Deactivate" @if(@$user->status == 'Deactivate') selected @endif>Deactivate</option>
                                    </select>
                                    @if ($errors->has('status'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Submit" class="btn btn-primary">
                                <a href="{{url('/admin/manage-users')}}" class="btn btn-info">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
                <!--widget card ends-->



            </div>
        </div>


    </div>
</section>
<script type="text/javascript">
    $('#add_edit_user').validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 30,
            },
            type: {
                required: true,
            },
            password_confirmation: {
                equalTo: '#password',
            },
        },
        errorPlacement: function(error, element) {

            if ($(element).hasClass('js-select2')) {
                error.appendTo(element.parent());
            } else {
                error.addClass('mt-2 text-danger');
                error.insertAfter(element);
            }
        },
    });
</script>
@endsection