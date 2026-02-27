@props(['name', 'value' => null])

@php
    $startDate = now()->subDays(5);
    $endDate = now()->addDays(30);
    $currentDate = $value ? \Carbon\Carbon::parse($value) : now();
@endphp

<div x-data="customPicker({
    name: '{{ $name }}',
    initialValue: '{{ $value }}'
})" class="space-y-6">
    <input type="hidden" :name="name" :value="formattedValue">

    <!-- Day Picker -->
    <div>
        <label class="block font-bold text-xs uppercase tracking-widest text-cyan-400 mb-2">Selecciona el día</label>
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-[#030308] to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-[#030308] to-transparent z-10 pointer-events-none"></div>
            
            <div 
                class="flex gap-3 overflow-x-auto hide-scrollbar py-4 px-[calc(50%-2rem)] gradient-mask-x scroll-smooth"
                x-ref="dayPicker"
                @wheel.prevent="$el.scrollLeft += $event.deltaY"
            >
                @php
                    $tempDate = $startDate->copy();
                    $lastMonth = null;
                @endphp
                @while($tempDate <= $endDate)
                    @php
                        $day = $tempDate->day;
                        $month = $tempDate->month;
                        $monthName = $tempDate->format('F');
                        $year = $tempDate->year;
                        $showMonth = $lastMonth !== $month;
                        $lastMonth = $month;
                    @endphp
                    
                    <div class="flex flex-col items-center">
                        @if($showMonth)
                            <span class="text-[8px] text-white/30 uppercase font-black mb-1 tracking-tighter">{{ $monthName }}</span>
                        @else
                            <span class="h-3"></span>
                        @endif
                        <div 
                            @click="setDay({{ $day }}, {{ $month - 1 }}, {{ $year }})"
                            id="day-{{ $year }}-{{ $month }}-{{ $day }}"
                            :class="(day == {{ $day }} && month == {{ $month - 1 }} && year == {{ $year }}) ? 'picker-item active' : 'picker-item inactive'"
                            class="flex-shrink-0"
                        >
                            <span class="text-[10px] uppercase font-bold">{{ $tempDate->format('D') }}</span>
                            <span class="text-xl font-bold">{{ $day }}</span>
                        </div>
                    </div>
                    @php $tempDate->addDay(); @endphp
                @endwhile
            </div>
        </div>
    </div>

    <!-- Time Picker (Hours) -->
    <div>
        <label class="block font-bold text-xs uppercase tracking-widest text-cyan-400 mb-2">Hora</label>
        <div class="relative group">
            <div 
                class="flex gap-2 overflow-x-auto hide-scrollbar py-2 px-[calc(50%-1.5rem)] gradient-mask-x scroll-smooth"
                x-ref="hourPicker"
                @wheel.prevent="$el.scrollLeft += $event.deltaY"
            >
                @for($h = 0; $h < 24; $h++)
                    <div 
                        @click="setHour({{ $h }})"
                        id="hour-{{ $h }}"
                        :class="hour == {{ $h }} ? 'picker-item active !w-12 !h-12' : 'picker-item inactive !w-12 !h-12'"
                        class="flex-shrink-0"
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
                class="flex gap-2 overflow-x-auto hide-scrollbar py-2 px-[calc(50%-1.5rem)] gradient-mask-x scroll-smooth"
                x-ref="minutePicker"
                @wheel.prevent="$el.scrollLeft += $event.deltaY"
            >
                @for($m = 0; $m < 60; $m += 5)
                    <div 
                        @click="setMinute({{ $m }})"
                        id="minute-{{ $m }}"
                        :class="minute == {{ $m }} ? 'picker-item active !w-12 !h-12' : 'picker-item inactive !w-12 !h-12'"
                        class="flex-shrink-0"
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
            month: initialDate.getMonth(),
            year: initialDate.getFullYear(),
            hour: initialDate.getHours(),
            minute: Math.round(initialDate.getMinutes() / 5) * 5,

            init() {
                this.$nextTick(() => {
                    this.scrollToSelected('day', `${this.year}-${this.month + 1}-${this.day}`);
                    this.scrollToSelected('hour', this.hour);
                    this.scrollToSelected('minute', this.minute);
                });
            },

            setDay(d, m, y) { 
                this.day = d; 
                this.month = m;
                this.year = y;
                this.scrollToSelected('day', `${y}-${m + 1}-${d}`);
            },
            setHour(h) { 
                this.hour = h; 
                this.scrollToSelected('hour', h);
            },
            setMinute(m) { 
                this.minute = m; 
                this.scrollToSelected('minute', m);
            },

            scrollToSelected(type, value) {
                const container = this.$refs[type + 'Picker'];
                const element = document.getElementById(type + '-' + value);
                if (container && element) {
                    const scrollLeft = element.offsetLeft - (container.offsetWidth / 2) + (element.offsetWidth / 2);
                    container.scrollTo({ left: scrollLeft, behavior: 'smooth' });
                }
            },

            get formattedValue() {
                let d = new Date(this.year, this.month, this.day, this.hour, this.minute);
                let yyyy = d.getFullYear();
                let mm = String(d.getMonth() + 1).padStart(2, '0');
                let dd = String(d.getDate()).padStart(2, '0');
                let hh = String(d.getHours()).padStart(2, '0');
                let min = String(d.getMinutes()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd} ${hh}:${min}`;
            }
        }
    }
</script>
