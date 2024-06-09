@include('layouts.header')
    @push('styles')
        <style>
            /* Custom styles */
            .container {
                margin-top: 50px;
            }
            .random-string {
                font-size: 24px;
                margin-bottom: 20px;
            }
            .words-list {
                margin-top: 20px;
            }
            .words-list span {
                margin-right: 10px;
            }
            .score {
                margin-top: 20px;
            }
        </style>
    @endpush
    <body>
        <div class="container">
            <p class="lead text-secondary"> Welcome, {{ $participant->name }}. You joined with {{ $participant->email }}</p>
            <div class="row">            
                <div class="col-md-3">
                    <div class="card card-default">
                        <div class="card-header">
                            <p class="card-title">New Word</p>
                        </div>
                        <div class="card-body">
                            <form  method="post" action="" id="word-form">
                                <div class="form-group">
                                    <label for="word">Enter a Word:</label>
                                    <input type="text" class="form-control" id="word" placeholder="Enter a word">
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>                                                           
                        </div>
                        <div class="card-footer" id="word-response"></div>
                    </div>



                </div>
                <div class="col-md-9">
<!--                    @if(session()->has('random_string'))
                    <p class="random-string">String : <span class="text-secondary">{{ session('random_string') }}</span>
                    @endif-->

                    @if(session()->has('alphabets_left'))
                    <p class="random-string lead" >Available Alphabets: <span class="text-secondary" id="available_alphabets">{{ implode(' ', session('alphabets_left')) }}</span></p>
                    @endif

                    <div class="score">
                        <h4>Total Score: <span id="total_score">0</span></h4>                        
                    </div>
                    <div class="words-list">
                        <h4>Legal Words Made:</h4> 
                        <div id="word_cloud"></div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-md btn-danger" id="end_game">End Game</button>
                </div>
            </div>    
        </div>



        
@push('scripts')
<script>
    $(document).ready(function () {
        $('#word-form').on('submit', function (e) {
            e.preventDefault();
            const word = $('#word').val().toLowerCase().replace(/[^a-z]/g, ''); // Remove non-alphabetic characters
            const csrfToken = "{{ csrf_token() }}";
            // Ajax starts here
            $.ajax({
                url: "{{ route('puzzler.validateWord') }}", // Assuming the route is named 'puzzler.checkWord'
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                type: 'POST',
                data: {word: word},
                dataType: 'json', // Expect JSON response
                success: function (response) {
                    // Handle successful response
                    if (response.success) {
                        $('#word-response').html(response.message);
                        $('#total_score').text(response.total_score);
                        $('#word_cloud').append('<span>' + response.word + ': ' + response.word_score + '</span>');
                        $('#available_alphabets').text(response.alphabets_left);
                    } else {
                        $('#word-response').html(response.message); // Display error message
                    }
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    $('#word-response').html('Error: ' + error); // Display error message
                },
                beforeSend: function () {
                    // Before sending the request
                    // You can show a loading spinner or disable the submit button
                },
                complete: function () {
                    // After completing the request (success or error)
                    // You can hide the loading spinner or enable the submit button
                }
            });
            // Ajax ends here
        });
        
        //End Game

            $('#end_game').on('click', function() {
            const csrfToken = "{{ csrf_token() }}";
                $.ajax({
                    url: "{{ route('puzzler.endGame') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#word-response').text(response.message); // Display success message
                            // Optionally, you can redirect the user or update the UI
                        } else {
                            $('#word-response').text(response.message); // Display error message
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#word-response').text('Error: ' + error); // Display error message
                    }
                });
            });
    
    });
        </script>
        @endpush
@include('layouts.footer')