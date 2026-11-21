<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report card, {{ $student->name }} — {{ config('app.name') }}</title>

    {{-- Own minimal print layout: always black on white, whatever theme the app is in. --}}
    <style>
        body { margin: 0; padding: 1.5rem; background: #fff; color: #000; font-family: Arial, Helvetica, sans-serif; line-height: 1.5; }
        .sheet { max-width: 48rem; margin: 0 auto; }
        .no-print { margin-bottom: 1.5rem; display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
        .no-print a { color: #0b5a73; }
        .no-print button { padding: 0.5rem 1.25rem; border: 2px solid #000; background: #000; color: #fff; font: inherit; font-weight: 700; cursor: pointer; border-radius: 0.4rem; }
        .no-print button:focus-visible, .no-print a:focus-visible { outline: 3px solid #0b5a73; outline-offset: 2px; }
        .school { margin: 0; font-size: 1.1rem; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; text-align: center; }
        h1 { margin: 0.25rem 0 1.25rem; font-size: 1.8rem; text-align: center; }
        dl { display: grid; grid-template-columns: max-content 1fr; gap: 0.25rem 1rem; margin: 0 0 1.5rem; }
        dt { font-weight: 700; }
        dd { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        caption { margin-bottom: 0.5rem; text-align: left; font-weight: 700; }
        th, td { border: 1px solid #000; padding: 0.5rem 0.6rem; text-align: center; }
        thead th { background: #e8e8e8; }
        tbody th { text-align: left; }
        tfoot th, tfoot td { border-top: 3px double #000; font-size: 1.1rem; }
        .table-wrap { overflow-x: auto; }
        .note { margin-top: 1rem; font-size: 0.9rem; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            thead th { background: #e8e8e8; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="no-print">
            <a href="{{ route('grades.index') }}">Back to grades</a>
            <button type="button" onclick="window.print()">Print</button>
        </div>

        <main>
            <p class="school">{{ config('app.name') }}</p>
            <h1>Report card</h1>

            <dl>
                <dt>Student</dt>
                <dd>{{ $student->name }}</dd>
                <dt>Grade level</dt>
                <dd>{{ $student->grade_level }}</dd>
                <dt>Date printed</dt>
                <dd>{{ now()->format('M d, Y') }}</dd>
            </dl>

            @if ($subjects->isEmpty())
                <p>No grades have been encoded for this student yet.</p>
            @else
                <div class="table-wrap">
                    <table>
                        <caption>Grades by subject and quarter</caption>
                        <thead>
                            <tr>
                                <th scope="col">Subject</th>
                                @foreach (\App\Models\Grade::QUARTERS as $quarter)
                                    <th scope="col">Quarter {{ $quarter }}</th>
                                @endforeach
                                <th scope="col">Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject => $row)
                                <tr>
                                    <th scope="row">{{ $subject }}</th>
                                    @foreach (\App\Models\Grade::QUARTERS as $quarter)
                                        <td>{{ $row['quarters'][$quarter] ?? '—' }}</td>
                                    @endforeach
                                    <td>{{ number_format($row['final'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="row" colspan="{{ count(\App\Models\Grade::QUARTERS) + 1 }}">General average</th>
                                <td>{{ number_format($generalAverage, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p class="note">The final grade of a subject is the average of its quarters. The general average is the average of the final grades.</p>
            @endif
        </main>
    </div>
</body>
</html>
