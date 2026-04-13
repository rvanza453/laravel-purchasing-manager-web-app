@if(isset($globalAnnouncements) && count($globalAnnouncements) > 0)
    <div id="global-announcements-container" class="fixed top-0 left-0 w-full z-[9999] flex flex-col pointer-events-none">
        @foreach($globalAnnouncements as $ann)
            @php
                $bgColor = 'bg-blue-600';
                $icon = 'fa-info-circle';
                if($ann->type === 'warning') { $bgColor = 'bg-yellow-500 text-yellow-900'; $icon = 'fa-triangle-exclamation'; }
                if($ann->type === 'maintenance') { $bgColor = 'bg-red-600'; $icon = 'fa-hammer'; }
                if($ann->type === 'update') { $bgColor = 'bg-green-600'; $icon = 'fa-circle-arrow-up'; }
            @endphp
            <div class="{{ $bgColor }} text-white px-4 py-2 shadow-md flex items-center justify-between pointer-events-auto transition-all" id="ann-{{ $ann->id }}">
                <div class="flex items-center gap-3 w-full animate-pulse-slow max-w-7xl mx-auto">
                    <i class="fa-solid {{ $icon }} text-lg"></i>
                    <div>
                        <strong class="font-bold mr-2 text-sm">{{ $ann->title }}</strong>
                        <span class="text-sm opacity-90 hidden sm:inline">{{ $ann->content }}</span>
                    </div>
                </div>
                <button onclick="dismissAnnouncement({{ $ann->id }})" class="text-white hover:text-gray-200 focus:outline-none opacity-80 hover:opacity-100 ml-4 shrink-0 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endforeach
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updatePadding() {
                var banner = document.getElementById('global-announcements-container');
                if(banner && banner.offsetHeight > 0) {
                    var currentPadding = parseInt(window.getComputedStyle(document.body).paddingTop) || 0;
                    document.body.style.paddingTop = (banner.offsetHeight) + 'px';
                } else {
                    document.body.style.paddingTop = '0px';
                }
            }
            updatePadding();
            
            window.dismissAnnouncement = function(id) {
                var el = document.getElementById('ann-' + id);
                if(el) {
                    el.style.display = 'none';
                    updatePadding();
                }
            };
        });
    </script>
    <style>
        .animate-pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
@endif
