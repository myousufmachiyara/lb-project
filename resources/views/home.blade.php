@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
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
                    <h3 class="amount text-dark"><strong>Total Pieces</strong></h3>
                    <h2 class="amount m-0 text-danger actual-data">
                        <strong>{{ $totalPieces }}</strong>
                        <!-- <span class="title text-end text-dark h6"> PKR</span> -->
                    </h2>
                    <div class="summary-footer">
                        <a class="text-danger text-uppercase" href="#">View Details</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection