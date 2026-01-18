

<x-filament-panels::page>
	@php
		$user = auth()->user();
	@endphp
	<main class="flex flex-col items-center justify-start py-16 px-4 min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-100">
		<div class="w-full max-w-5xl">
			<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-12 bg-white/80 rounded-2xl shadow p-10">
				<!-- Left: Info -->
				<div class="flex-1 min-w-0">
					<h2 class="text-3xl font-bold mb-6">My Account</h2>
					<div class="text-4xl font-extrabold mb-2">{{ $user->name }}</div>
					<div class="flex items-center gap-6 mb-4">
						<span class="flex items-center gap-2 text-lg"><span class="material-icons">mail</span>{{ $user->email }}</span>
						<span class="flex items-center gap-2 text-lg"><span class="material-icons">phone</span>{{ $user->contact_number ?? $user->email }}</span>
					</div>
					<div class="text-base text-gray-800 mb-4" style="max-width: 500px;">
						{{ $user->bio ?? 'No bio provided' }}
					</div>
				</div>
				<!-- Right: Profile Picture and Role -->
				<div class="flex flex-col items-center gap-4">
					<div class="w-64 h-64 rounded-full bg-gradient-to-b from-blue-100 to-green-100 flex items-center justify-center overflow-hidden">
						@if ($user->pfp)
							<img src="{{ asset('storage/' . $user->pfp) }}" alt="Profile Picture" class="object-cover w-full h-full rounded-full">
						@else
							<img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=256" alt="Avatar" class="object-cover w-full h-full rounded-full">
						@endif
					</div>
					<span class="inline-block mt-2 px-8 py-2 rounded-full bg-green-400 text-white font-bold text-lg tracking-wide shadow">{{ strtoupper($user->role ?? 'ROLE') }}</span>
				</div>
			</div>
			<div class="flex justify-end mt-8">
				<a href="{{ \App\Filament\Resources\Accounts\AccountResource::getUrl('edit', ['record' => $user->id]) }}" class="inline-flex items-center gap-2 px-6 py-2 rounded-lg bg-green-400 hover:bg-green-500 text-white font-semibold shadow transition"><span class="material-icons">edit</span> Edit Account</a>
			</div>
		</div>
		<!-- Material Icons -->
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	</main>
</x-filament-panels::page>
