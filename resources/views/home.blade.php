@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div>
            <h2 class="text-dark"><strong id="currentDate"></strong></h2>
        </div>
        <div class="col-12 col-md-3 mb-2">
            <section class="card card-featured-left card-featured-primary mt-3">
                <div  style="background-image: url('/assets/img/rec-icon.png') " class="card-body icon-container">
                    <h3 class="amount text-dark"><strong>Total Projects</strong></h3>
                    <h2 class="amount m-0 text-primary actual-data">
                        <strong>{{ $totalProjects }}</strong>
                        <!-- <span class="title text-end text-dark h6"> PKR</span> -->
                    </h2>
                    <div class="summary-footer">
                        <a class="text-primary text-uppercase" href="#">View Details</a>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-md-3 mb-2">
            <section class="card card-featured-left card-featured-danger mt-3">
                <div  style="background-image: url('/assets/img/bank-icon.png') " class="card-body icon-container">
                    <h3 class="amount text-dark"><strong>Pieces In Production</strong></h3>
                    <h2 class="amount m-0 text-danger actual-data">
                        <strong>{{ $totalPiecesInProcess }}</strong>
                        <!-- <span class="title text-end text-dark h6"> PKR</span> -->
                    </h2>
                    <div class="summary-footer">
                        <a class="text-danger text-uppercase" href="#">View Details</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <section class="card">
                <header class="card-header">
                    <div class="card-actions">
                        <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                    </div>
                    <h2 class="card-title">Todo List</h2>
                </header>
                <div class="card-body scrollable-div">
                    <table class="table table-responsive-md table-striped mb-0">
                        <thead class="sticky-tbl-header">
                            <tr>
                                <th>S.No</th>
                                <th>Task</th>
                                <th>Project</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingTasks as $index => $task)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $task->task_name }}</td>
                                    <td>{{ $task->project->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}</td>
                                    <td>{{ $task->status->name ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No upcoming tasks.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div> -->
    <script>

		$(document).ready(function() {
			// Get current date and day
			const now = new Date();
			const day = getDaySuffix(now.getDate());
			const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
			const currentDate = now.toLocaleDateString(undefined, options);

			// Format the date as "Thursday, 5th December 2024"
			const formattedDate = `${now.toLocaleString('en-GB', { weekday: 'long' })}, ${day} ${now.toLocaleString('en-GB', { month: 'long' })} ${now.getFullYear()}`;

			// Update UI
			document.getElementById('currentDate').innerText = formattedDate;
		});	

        function getDaySuffix(day) {
			if (day >= 11 && day <= 13) {
			return day + 'th';
			}
			switch (day % 10) {
			case 1: return day + 'st';
			case 2: return day + 'nd';
			case 3: return day + 'rd';
			default: return day + 'th';
			}
		}
    </script>
@endsection