<div class="space-y-4 text-sm">
    <div class="grid grid-cols-2 gap-4">
        <div><p class="font-semibold text-gray-500">Full Name</p><p>{{ $record->first_name }} {{ $record->last_name }}</p></div>
        <div><p class="font-semibold text-gray-500">Role Applied</p><p>{{ $record->role }}</p></div>
        <div><p class="font-semibold text-gray-500">Email</p><p>{{ $record->email }}</p></div>
        <div><p class="font-semibold text-gray-500">Phone</p><p>{{ $record->phone }}</p></div>
        <div><p class="font-semibold text-gray-500">Institution</p><p>{{ $record->institution }}</p></div>
        <div><p class="font-semibold text-gray-500">Course / Field</p><p>{{ $record->course }}</p></div>
        <div><p class="font-semibold text-gray-500">Graduation Year</p><p>{{ $record->graduation_year }}</p></div>
        <div><p class="font-semibold text-gray-500">Session</p><p>{{ $record->session->name ?? '—' }}</p></div>
        @if($record->linkedin_url)
        <div><p class="font-semibold text-gray-500">LinkedIn</p><p><a href="{{ $record->linkedin_url }}" target="_blank" class="text-blue-600 underline">View Profile</a></p></div>
        @endif
        @if($record->portfolio_url)
        <div><p class="font-semibold text-gray-500">Portfolio</p><p><a href="{{ $record->portfolio_url }}" target="_blank" class="text-blue-600 underline">View Portfolio</a></p></div>
        @endif
    </div>
    @if($record->cover_letter)
    <div>
        <p class="font-semibold text-gray-500 mb-1">Cover Letter</p>
        <div class="bg-gray-50 rounded-lg p-4 text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $record->cover_letter }}</div>
    </div>
    @endif
</div>
