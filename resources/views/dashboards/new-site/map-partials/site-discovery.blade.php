@foreach ($data as $site)
    <div class="msjk-site-item" data-site-id="{{ $site->id }}"
        data-site-data="{{ json_encode([
            'id' => $site->id,
            'name' => $site->name,
            'address' => $site->address,
            'lat' => $site->lat,
            'lng' => $site->lng,
            'estimated_paint_litres' => $site->estimated_paint_litres,
            'next_followup_at' => $site->next_followup_at,
            'status' => $site->status,
            'siteType' => $site->siteType ? ['name' => $site->siteType->name] : null,
            'currentStage' => $site->currentStage ? ['name' => $site->currentStage->name] : null,
            'date' => $site->date,
            'photos' => $site->photos->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'file_path' => $photo->file_path,
                    'photo_type' => $photo->photo_type,
                ];
            }),
        ]) }}">
        <div class="clearfix">
            <h4 class="pull-left">{{ $site->name }}</h4>
            <span class="msjk-status-active pull-right msjk-status-{{ $site->status }}">
                {{ ucwords(str_replace('_', ' ', $site->status)) }}
            </span>
        </div>

        <div class="msjk-site-meta">
            <span class="msjk-site-meta-icon">
                <svg fill="#87898b" width="16px" height="16px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M3.135 6.89c.933-.725 1.707-.225 2.74.971.116.135.272-.023.361-.1.088-.078 1.451-1.305 1.518-1.361.066-.059.146-.169.041-.292a36.238 36.238 0 0 1-.743-.951c-1.808-2.365 4.946-3.969 3.909-3.994-.528-.014-2.646-.039-2.963-.004-1.283.135-2.894 1.334-3.705 1.893-1.061.726-1.457 1.152-1.522 1.211-.3.262-.048.867-.592 1.344-.575.503-.934.122-1.267.414-.165.146-.627.492-.759.607-.133.117-.157.314-.021.471 0 0 1.264 1.396 1.37 1.52.105.122.391.228.567.071.177-.156.632-.553.708-.623.078-.066-.05-.861.358-1.177zm5.708.517c-.12-.139-.269-.143-.397-.029L7.012 8.63a.289.289 0 0 0-.027.4l8.294 9.439c.194.223.53.246.751.053l.97-.813a.54.54 0 0 0 .052-.758L8.843 7.407zM19.902 3.39c-.074-.494-.33-.391-.463-.182-.133.211-.721 1.102-.963 1.506-.24.4-.832 1.191-1.934.41-1.148-.811-.749-1.377-.549-1.758.201-.383.818-1.457.907-1.59.089-.135-.015-.527-.371-.363-.357.164-2.523 1.025-2.823 2.26-.307 1.256.257 2.379-.85 3.494l-1.343 1.4 1.349 1.566 1.654-1.57c.394-.396 1.236-.781 1.998-.607 1.633.369 2.524-.244 3.061-1.258.482-.906.402-2.814.327-3.308zM2.739 17.053a.538.538 0 0 0 0 .758l.951.93c.208.209.538.121.746-.088l4.907-4.824-1.503-1.714-5.101 4.938z">
                    </path>
                </svg>
                <span>{{ $site->currentStage->name ?? 'N/A' }}</span>
            </span>
            <span class="msjk-site-meta-icon">
                <svg fill="#87898b" width="16px" height="16px" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M19,4H17V3a1,1,0,0,0-2,0V4H9V3A1,1,0,0,0,7,3V4H5A3,3,0,0,0,2,7V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V7A3,3,0,0,0,19,4Zm1,15a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V12H20Zm0-9H4V7A1,1,0,0,1,5,6H7V7A1,1,0,0,0,9,7V6h6V7a1,1,0,0,0,2,0V6h2a1,1,0,0,1,1,1Z">
                    </path>
                </svg>
                <span>{{ $site->date ?? 'N/A' }}</span>
            </span>
        </div>
    </div>
@endforeach
