@props(['name', 'value' => null])

@php
    $currentDate = $value ? \Carbon\Carbon::parse($value) : now();
    $daysInMonth = $currentDate->daysInMonth;
    $startDay = 1;
    $endDay = $daysInMonth;
@endphp

<div x-data="customPicker({
    name: '{{ $name }}',
    initialValue: '{{ $value }}'
})" class="space-y-6">
    <input type="hidden" :name="name" :value="formattedValue">

    <!-- Day Picker -->
    <div>
        <label class="block font-bold text-xs uppercase tracking-widest text-cyan-400 mb-2">Selecciona el día ({{ $currentDate->format('F Y') }})</label>
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-[#030308] to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-[#030308] to-transparent z-10 pointer-events-none"></div>
            
            <div 
                class="flex gap-3 overflow-x-auto hide-scrollbar py-4 px-12 gradient-mask-x"
                @wheel.prevent="$el.scrollLeft += $event.deltaY"
            >
                @for($i = 1; $i <= $daysInMonth; $i++)
                    @php 
                        $date = $currentDate->copy()->day($i);
                        $isToday = $date->isToday();
                    @endphp
                    <div 
                        @click="setDay({{ $i }})"
                        :class="day == {{ $i }} ? 'picker-item active' : 'picker-item inactive'"
                    >
                        <span class="text-[10px] uppercase font-bold">{{ $date->format('D') }}</span>
                        <span class="text-xl font-bold">{{ $i }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Time Picker (Hours) -->
    <div>
        <label class="block font-bold text-xs uppercase tracking-widest text-cyan-400 mb-2">Hora</label>
        <div class="relative group">
            <div 
                class="flex gap-2 overflow-x-auto hide-scrollbar py-2 px-4 gradient-mask-x"
                @wheel.prevent="$el.scrollLeft += $event.deltaY"
            >
                @for($h = 0; $h < 24; $h++)
                    <div 
                        @click="setHour({{ $h }})"
                        :class="hour == {{ $h }} ? 'picker-item active !w-12 !h-12' : 'picker-item inactive !w-12 !h-12'"
                    >
                        <span class="text-sm font-bold">{{ sprintf('%02d', $h) }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Time Picker (Minutes) -->
    <div>
        <label class="block font-bold text-xs uppercase tracking-widest text-cyan-400 mb-2">Minutos</label>
        <div class="relative group">
            <div 
                class="flex gap-2 overflow-x-auto hide-scrollbar py-2 px-4 gradient-mask-x"
                @wheel.prevent="$el.scrollLeft += $event.deltaY"
            >
                @for($m = 0; $m < 60; $m += 5)
                    <div 
                        @click="setMinute({{ $m }})"
                        :class="minute == {{ $m }} ? 'picker-item active !w-12 !h-12' : 'picker-item inactive !w-12 !h-12'"
                    >
                        <span class="text-sm font-bold">{{ sprintf('%02d', $m) }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<script>
    function customPicker(config) {
        let initialDate = config.initialValue ? new Date(config.initialValue) : new Date();
        
        return {
            name: config.name,
            day: initialDate.getDate(),
            hour: initialDate.getHours(),
            minute: Math.round(initialDate.getMinutes() / 5) * 5,
            month: initialDate.getMonth(),
            year: initialDate.getFullYear(),

            setDay(d) { this.day = d; },
            setHour(h) { this.hour = h; },
            setMinute(m) { this.minute = m; },

            get formattedValue() {
                let d = new Date(this.year, this.month, this.day, this.hour, this.minute);
                return d.toISOString().slice(0, 16).replace('T', ' ');
            }
        }
    }
</script>
