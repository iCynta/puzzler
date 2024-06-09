@include('layouts.header')
<body>
    <div class="container vh-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('participants.store')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <label for="name" class="col-sm-3 col-form-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" required="true" class="form-control" id="name" placeholder="Name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" required="true" class="form-control" id="email" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('layouts.footer')

