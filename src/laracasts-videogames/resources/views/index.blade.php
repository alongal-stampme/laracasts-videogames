@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h2 class="text-blue-500 uppercase tracking-wide font-semibold">Popular Games</h2>
        <livewire:popular-games />
        <div class="flex flex-col lg:flex-row my-10">
            <div class="recently-reviewed w-full lg:w-3/4 mr-0 lg:mr-32">
                <h2 class="text-blue-500 uppercase tracking-wide font-semibod">Recently Reviewed</h2>
                <livewire:recently-reviewed />
            </div>
            <div class="most-anticipated lg:w-1/4 mt-12 lg:mt-0">
                <h2 class="text-blue-500 uppercase tracking-wide font-semibod">Most Anticipated</h2>
                <div class="most-anticipated-container space-y-10 mt-8">
                    @foreach ($mostAnticipated as $game)
                        <div class="game flex">
                            <a href="#">
                                <img src="{{ Str::replaceFirst('thumb', 'cover_small', $game['cover']['url']) }}" alt="game cover" class="w-16 hover:opacity-75 transition ease-in-out duration-150">
                            </a>
                            <div class="ml-4">
                                <a href="#" class="hover:text-gray-300">{{ $game['name'] }}</a>
                                <div class="text-gray-400-text-sm-mt-1">
                                    {{ Carbon\Carbon::parse($game['first_release_date'])->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <h2 class="text-blue-500 uppercase tracking-wide font-semibod mt-12">Coming Soon</h2>
                <div class="coming-soon-container space-y-10 mt-8">
                    @foreach ($comingSoon as $game)
                        <div class="game flex">
                            <a href="#">
                                <img src="{{ Str::replaceFirst('thumb', 'cover_small', $game['cover']['url']) }}" alt="game cover" class="w-16 hover:opacity-75 transition ease-in-out duration-150">
                            </a>
                            <div class="ml-4">
                                <a href="#" class="hover:text-gray-300">{{ $game['name'] }}</a>
                                <div class="text-gray-400-text-sm-mt-1">
                                    {{ Carbon\Carbon::parse($game['first_release_date'])->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div> <!-- end container -->
@endsection
