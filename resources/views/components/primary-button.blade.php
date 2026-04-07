<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-[1.5rem] font-black text-xs text-white uppercase tracking-[0.2em] transition-all shadow-xl shadow-indigo-500/30 active:scale-95 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
