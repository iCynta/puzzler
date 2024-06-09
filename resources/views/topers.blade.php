@include('layouts.header')

<div class="row">
    <div class="page-header text-center my-4">
        <h1>Meet Our Toppers</h1>
        <div class="word-cloud">
            <p>Highest Scoring Words : 
            @forelse($topScoringWords as $word => $score)
            
                <span class="badge badge-success" style="margin-right:10px">{{ $word }} : {{$score}}</span>
            
            @empty
                <span class="badge badge-success" style="margin-right:10px">No word found</span>
            @endforelse
            </p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-striped table-bordered">
            <thead class="bg-secondary">
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Score</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topers as $toper)
                <tr>                    
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $toper->participant->name }}</td>
                    <td>
                        <span>{{ $toper->score ?? 0 }}</span>
                    </td>
                    <td>{{ $toper->formatted_created_at }}</td>
                    <td>
                        <!-- Button to Trigger Modal -->
                        <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#detailsModal{{ $toper->id }}">
                            VIEW DETAILS
                        </button>

                        <!-- Modal for Details -->
                        <div class="modal fade" id="detailsModal{{ $toper->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel{{ $toper->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailsModalLabel{{ $toper->id }}">Details for {{ $toper->participant->name }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        @if($toper->detail)
                                            <!-- Display Details -->
                                            <p><strong>Participant Name:</strong> {{ $toper->participant->name }}</p>
                                            <p><strong>Email:</strong> {{ $toper->participant->email }}</p>
                                            <p><strong>Total Score:</strong> {{ $toper->score }}</p>
                                            <p><strong>Date:</strong> {{ $toper->formatted_created_at }}</p>
                                            <p><strong>Words and Scores:</strong></p>
                                            <ul class="list-group">
                                                @php
                                                    $wordsScores = json_decode($toper->detail->words_scores ?? '[]', true);
                                                @endphp
                                                @if(is_array($wordsScores))
                                                    @foreach($wordsScores as $entry)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $entry['word'] }}
                                                            <span class="badge badge-primary badge-pill">{{ $entry['score'] }}</span>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        @else
                                            <!-- No Data Message -->
                                            <p>No details available.</p>
                                        @endif
                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">No Data found!!</td>                    
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right bg-dark">
                        <a href="{{route('welcome')}}" class="btn btn-md btn-success">Back </a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@include('layouts.footer')
