@extends('Admin.Layout.app')
@section('title', 'Manage'.' '.$label.'s')
@section('content')

<section class="admin-content">
    <div class="bg-dark">
        <div class="container  m-b-30">
            <div class="row">
                <div class="col-10 text-white p-t-40 p-b-90">

                    <h4 class="">
                        Manage {{@$label}}s
                    </h4>
                </div>
                <div class="col-2 text-white p-t-40 p-b-90">
                    <a href="{{url('/admin/add-user')}}" class="add_record">Add {{@$label}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container  pull-up">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive p-t-10">
                            <table id="example" class="table " style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Profile pic</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                    <tr>
                                        <td>{{$user->full_name}}</td>
                                        <td>{{$user->profile_image}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->mobile_number}}</td>
                                        <td>{{$user->status}}</td>
                                        <td>
                                            <a href="{{ url('admin/edit-user/'.$user['id']) }}" title="Edit"><i class="fa fa-edit"></i></a>
                                            <a href="{{ url('admin/delete-user/'.$user['id']) }}" class="del_btn" title="Delete"><i class="fa fa-trash"></i></a>

                                        </td>
                                        
                                    </tr>
                                    @empty
                                        <tr>
                                            <td>No data Found!</td>
                                        </tr>
                                    @endforelse
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection()