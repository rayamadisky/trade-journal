@extends('layouts.app')

@section('title', 'TradeRitual — Performance Calendar')

@section('content')
<div class="px-4 py-6 pb-24 page-enter" x-data="performanceCalendar({ trades: {{ $tradesJson }}, dailySummary: {{ $dailyJson }} })">
    
    {{-- Header & Month Selector --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Performance</h1>
        
        <div class="flex items-center gap-3 bg-gray-800 rounded-xl p-1">
            <button @click="prevMonth()" class="p-2 text-gray-400 hover:text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <div class="text-white font-bold w-32 text-center" x-text="`${monthNames[month]} ${year}`"></div>
            <button @click="nextMonth()" class="p-2 text-gray-400 hover:text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>

    {{-- MT5 Filter Instruction --}}
    <p class="text-gray-500 text-xs text-center mb-4">Click a start date and an end date to filter total profit.</p>

    {{-- Calendar Grid --}}
    <div class="glass p-4 rounded-3xl mb-6 select-none">
        {{-- Day Headers --}}
        <div class="grid grid-cols-7 gap-1 mb-2">
            <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']">
                <div class="text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider" x-text="day"></div>
            </template>
        </div>

        {{-- Days --}}
        <div class="grid grid-cols-7 gap-1.5">
            {{-- Blank Days --}}
            <template x-for="blank in blankDays">
                <div class="aspect-square rounded-xl bg-gray-800/30"></div>
            </template>

            {{-- Actual Days --}}
            <template x-for="d in monthDays" :key="d.dateStr">
                <div 
                    @click="selectDate(d.dateStr)"
                    class="aspect-square rounded-xl flex flex-col items-center justify-center p-1 cursor-pointer transition-all border-2"
                    :class="{
                        'bg-green-500/20 text-green-400 border-transparent': d.hasTrade && d.pnl > 0 && !isSelected(d.dateStr),
                        'bg-red-500/20 text-red-400 border-transparent': d.hasTrade && d.pnl <= 0 && !isSelected(d.dateStr),
                        'bg-gray-800/50 text-gray-500 border-transparent': !d.hasTrade && !isSelected(d.dateStr),
                        'border-purple-500 shadow-[0_0_15px_rgba(168,85,247,0.5)] scale-105 z-10': isSelected(d.dateStr) && d.hasTrade,
                        'border-purple-500 bg-purple-500/10 scale-105 z-10': isSelected(d.dateStr) && !d.hasTrade
                    }"
                >
                    <span class="text-xs font-bold mb-1" :class="isSelected(d.dateStr) ? 'text-white' : ''" x-text="d.day"></span>
                    
                    <template x-if="d.hasTrade">
                        <span class="text-[9px] font-bold" x-text="(d.pnl > 0 ? '+' : '') + Math.round(d.pnl)"></span>
                    </template>
                    <template x-if="!d.hasTrade">
                        <span class="text-[9px] opacity-0">-</span>
                    </template>
                </div>
            </template>
        </div>
    </div>

    {{-- MT5 Style Selected Range Analytics --}}
    <div x-show="startDate" x-transition.opacity class="mb-6">
        <div class="bg-gradient-to-r from-purple-900/50 to-pink-900/50 border border-purple-500/30 p-5 rounded-3xl glow-purple relative overflow-hidden">
            <button @click="startDate = null; endDate = null" class="absolute top-4 right-4 text-purple-300 hover:text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            
            <p class="text-purple-300 text-xs font-bold uppercase tracking-wider mb-1">Selected Range Profit</p>
            <div class="flex items-end gap-2 mb-4">
                <h2 class="text-4xl font-bold" :class="selectedProfit() >= 0 ? 'text-green-400' : 'text-red-400'" x-text="(selectedProfit() >= 0 ? '+$' : '-$') + Math.abs(selectedProfit()).toFixed(2)"></h2>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-purple-500/20">
                <div>
                    <p class="text-purple-300/70 text-[10px] font-bold uppercase">Win Rate</p>
                    <p class="text-white font-medium" x-text="selectedWinRate() + '%'"></p>
                </div>
                <div>
                    <p class="text-purple-300/70 text-[10px] font-bold uppercase">Total Trades</p>
                    <p class="text-white font-medium" x-text="selectedTradesCount()"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Weekly Profit Breakdown --}}
    <div>
        <h3 class="text-lg font-bold text-white mb-4">Weekly Summary</h3>
        <div class="space-y-2">
            <template x-for="week in weeklyProfits" :key="week.weekNum">
                <div class="glass px-4 py-3 rounded-xl flex justify-between items-center" x-show="week.hasTrade">
                    <span class="text-gray-400 text-sm font-medium">Week <span x-text="week.weekNum"></span></span>
                    <span class="font-bold" :class="week.profit > 0 ? 'text-green-400' : (week.profit < 0 ? 'text-red-400' : 'text-gray-500')" x-text="(week.profit > 0 ? '+$' : (week.profit < 0 ? '-$' : '$')) + Math.abs(week.profit).toFixed(2)"></span>
                </div>
            </template>
            <div x-show="!weeklyProfits.some(w => w.hasTrade)" class="text-center text-gray-500 text-sm py-4">
                No trades logged this month.
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('performanceCalendar', (config) => ({
            trades: config.trades,
            dailySummary: config.dailySummary,
            
            // Set default view to current month
            month: new Date().getMonth(),
            year: new Date().getFullYear(),
            
            startDate: null,
            endDate: null,
            
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            
            get daysInMonth() {
                return new Date(this.year, this.month + 1, 0).getDate();
            },
            
            get blankDays() {
                let startDay = new Date(this.year, this.month, 1).getDay(); // 0 is Sunday
                return Array.from({length: startDay}, (_, i) => i);
            },
            
            get monthDays() {
                return Array.from({length: this.daysInMonth}, (_, i) => {
                    let d = i + 1;
                    let m = this.month + 1;
                    let dateStr = `${this.year}-${m.toString().padStart(2, '0')}-${d.toString().padStart(2, '0')}`;
                    let pnl = this.dailySummary[dateStr] || 0;
                    let hasTrade = Object.keys(this.dailySummary).includes(dateStr);
                    
                    return {
                        day: d,
                        dateStr: dateStr,
                        pnl: pnl,
                        hasTrade: hasTrade
                    };
                });
            },
            
            get weeklyProfits() {
                let weeks = [];
                let currentWeekProfit = 0;
                let currentWeekIndex = 1;
                let hasTradeInWeek = false;
                
                let allDays = [...this.blankDays.map(() => null), ...this.monthDays];
                
                for (let i = 0; i < allDays.length; i++) {
                    if (allDays[i] && allDays[i].hasTrade) {
                        currentWeekProfit += allDays[i].pnl;
                        hasTradeInWeek = true;
                    }
                    
                    // End of week (Saturday is index 6, 13, 20...) or end of month
                    if ((i + 1) % 7 === 0 || i === allDays.length - 1) {
                        weeks.push({ 
                            weekNum: currentWeekIndex++, 
                            profit: currentWeekProfit,
                            hasTrade: hasTradeInWeek 
                        });
                        currentWeekProfit = 0;
                        hasTradeInWeek = false;
                    }
                }
                return weeks;
            },
            
            selectDate(dateStr) {
                if (!this.startDate || (this.startDate && this.endDate)) {
                    // Click 1: Start selection
                    this.startDate = dateStr;
                    this.endDate = null;
                } else {
                    // Click 2: End selection
                    let start = new Date(this.startDate);
                    let current = new Date(dateStr);
                    
                    if (current < start) {
                        this.endDate = this.startDate;
                        this.startDate = dateStr;
                    } else {
                        this.endDate = dateStr;
                    }
                }
            },
            
            isSelected(dateStr) {
                if (!this.startDate) return false;
                
                let current = new Date(dateStr);
                let start = new Date(this.startDate);
                
                if (!this.endDate) {
                    return current.getTime() === start.getTime();
                }
                
                let end = new Date(this.endDate);
                return current >= start && current <= end;
            },
            
            _getSelectedTrades() {
                if (!this.startDate) return [];
                
                let start = new Date(this.startDate);
                // Compare only the date part to avoid timezone timezone shifts
                let startIso = start.toISOString().split('T')[0];
                
                let endIso = this.endDate ? new Date(this.endDate).toISOString().split('T')[0] : startIso;
                
                return this.trades.filter(t => {
                    return t.date >= startIso && t.date <= endIso;
                });
            },
            
            selectedProfit() {
                let selected = this._getSelectedTrades();
                return selected.reduce((sum, t) => sum + t.pnl, 0);
            },
            
            selectedTradesCount() {
                return this._getSelectedTrades().length;
            },
            
            selectedWinRate() {
                let selected = this._getSelectedTrades();
                if (selected.length === 0) return 0;
                
                let wins = selected.filter(t => t.pnl > 0).length;
                return Math.round((wins / selected.length) * 100);
            },
            
            prevMonth() {
                if (this.month === 0) {
                    this.month = 11;
                    this.year--;
                } else {
                    this.month--;
                }
                this.startDate = null;
                this.endDate = null;
            },
            
            nextMonth() {
                if (this.month === 11) {
                    this.month = 0;
                    this.year++;
                } else {
                    this.month++;
                }
                this.startDate = null;
                this.endDate = null;
            }
        }));
    });
</script>
@endpush
@endsection
