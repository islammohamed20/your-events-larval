@php
    $logs = isset($logs) ? $logs : collect([]);
    $title = isset($title) ? $title : 'سجل النشاط';
@endphp

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-list-ul me-2"></i>{{ $title }}</h5>
    </div>
    <div class="card-body">
        @if($logs && $logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>الوقت</th>
                            <th>الحدث</th>
                            <th>الوصف</th>
                            <th>الفاعل</th>
                            <th>التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            @php
                                $actor = $log->actor ?? null;
                                $actorLabel = $actor?->name ?? $actor?->email ?? (class_basename($log->actor_type) . ' #' . $log->actor_id);
                                $props = is_array($log->properties) ? $log->properties : (is_string($log->properties) ? json_decode($log->properties, true) : []);
                                $rowId = 'logProps_' . $log->id;
                            @endphp
                            <tr>
                                <td>
                                    <small class="text-muted">{{ $log->created_at->format('Y/m/d h:i A') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $log->action }}</span>
                                </td>
                                <td>{{ $log->description }}</td>
                                <td>
                                    <i class="fas fa-user me-1 text-muted"></i>
                                    {{ $actorLabel }}
                                </td>
                                <td>
                                    @if(is_array($props) && count($props) > 0)
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $rowId }}">
                                            <i class="fas fa-chevron-down"></i> عرض
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if(is_array($props) && count($props) > 0)
                                <tr class="collapse" id="{{ $rowId }}">
                                    <td colspan="5">
                                        <div class="p-2 border rounded bg-light">
                                            <div class="row g-2">
                                                @foreach($props as $key => $val)
                                                    <div class="col-md-4">
                                                        <div class="small">
                                                            <strong class="text-muted">{{ $key }}:</strong>
                                                            @if(is_array($val))
                                                                {{ implode('، ', array_map('strval', array_filter($val))) }}
                                                            @else
                                                                {{ (string) $val }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0"><i class="fas fa-info-circle me-1"></i>لا توجد سجلات نشاط بعد.</p>
        @endif
    </div>
</div>
