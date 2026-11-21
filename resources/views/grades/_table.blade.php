{{-- Subject x quarter table. Expects $grades: a collection of one student's Grade rows. --}}
@php $bySubject = $grades->groupBy('subject'); @endphp

@if ($bySubject->isEmpty())
    <p class="mb-0">No grades have been encoded yet.</p>
@else
    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0 ec-stack ec-stack-grades">
            <thead>
                <tr>
                    <th scope="col">Subject</th>
                    @foreach (\App\Models\Grade::QUARTERS as $quarter)
                        <th scope="col">Quarter {{ $quarter }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($bySubject as $subject => $rows)
                    <tr>
                        <th scope="row" class="ec-stack-title">{{ $subject }}</th>
                        @foreach (\App\Models\Grade::QUARTERS as $quarter)
                            <td data-label="Quarter {{ $quarter }}">{{ $rows->firstWhere('quarter', $quarter)?->grade ?? '—' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
