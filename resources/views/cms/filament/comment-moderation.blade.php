<div class="grid h-full">
    <div class="flex flex-col space-y-4">
        <div class="p-4 text-sm border rounded-lg border-slate-200">
            <span class="block font-bold">{{ __('common.fld-author') }}:</span>
            @if ($record->author)
                <span>{{ $record->author->name }} - </span>
                <span>{{ $record->author->email }}</span>
            @else
                <span>{{ __('comments.lbl-anonymous') }}</span>
            @endif
        </div>
        <div class="flex flex-col p-4 text-sm border rounded-lg border-slate-200">
            <span class="font-bold">{{ __('common.fld-body') }}:</span>
            <span>{{ $record->body }}</span>
        </div>

        <div class="flex justify-between w-full">
            <div class="w-1/2 p-4 mr-4 text-sm border rounded-lg border-slate-200">
                <div>
                    <span class="font-bold">{{ __('common.fld-status') }}:</span>
                    <span> {{ $record->status->getLabel() }}</span>
                </div>
                <div>
                    <span class="font-bold">{{ __('common.fld-status-changed-at') }}:</span>
                    <span>({{ $record->status_changed_at->diffForHumans() }})</span>
                </div>
            </div>
            <div class="w-1/2 p-4 text-sm border rounded-lg border-slate-200">
                <span class="font-bold">{{ __('common.fld-created-at') }}</span>
                <span> {{ \Carbon\Carbon::parse($record->created_at)->format('d/m/Y H:i:s') }}</span>
                <span class="text-xs italic">({{ $record->created_at->diffForHumans() }})</span>
            </div>
        </div>

    </div>

    <div class="flex items-end justify-start space-x-4">
        {{ $action->getModalAction('approve') }}
        {{ $action->getModalAction('reject') }}
        {{ $action->getModalAction('put-back-to-moderation') }}
    </div>
</div>
