@include('layouts.header')

    <div class="vh-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-md-4">
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
                                    <a href="{{route('puzzle.topers')}}" class="btn btn-md btn-secondary">Meet Our Topers</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                
                @if(request()->has('message'))
                <div class="alert alert-success" role="alert">
                    {{ request('message') }}
                </div>
                @endif
                @if(request()->has('words'))
                <div class="card mt-3">
                    <div class="card-body">                        
                        <p class="card-text">
                        @if(request()->has('words'))
                            @php
                                $words = json_decode(request('words'), true);
                                if ($words) {
                                    echo '<p>Score: ' . request('score') . '</p>';
                                    echo '<p>Legal Words: ';
                                    foreach ($words as $wordInfo) {
                                        echo '<span class="badge badge-success">' . $wordInfo['word'] . ' (' . $wordInfo['score'] . ') </span> ';
                                    }
                                    echo '</p>';
                                }
                            @endphp
                        @endif 
                        </p>
                    </div>
                </div>
                @endif                
            </div>

            
        </div>
    </div>
    @include('layouts.footer')

