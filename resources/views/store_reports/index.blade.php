@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Store Reports</h1>
        <a href="{{ route('store_reports.create') }}" class="btn btn-primary mb-3">Add New Report</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Store Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                    <tr>
                        <td>{{ $report->date }}</td>
                        <td>{{ $report->store_name }}</td>
                        <td>
                            <a href="{{ route('store_reports.show', $report) }}" class="btn btn-info">View</a>
                            <a href="{{ route('store_reports.edit', $report) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('store_reports.destroy', $report) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
