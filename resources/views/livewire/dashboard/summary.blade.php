<div class="flex h-full gap-6">
    <!-- Left Column: Orders and Menus -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Orders List section -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Orders List</h2>
                <a href="#" class="text-sm text-gray-500 hover:text-gray-900 font-medium">View all orders</a>
            </div>
            
            <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                <!-- Order Card 1 -->
                <div class="min-w-[280px] bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <span class="font-bold text-gray-900">Takeaway</span>
                        </div>
                        <span class="px-2.5 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-lg">Waiting</span>
                    </div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-semibold text-gray-900 text-sm">Juna Wok</span>
                        <span class="text-gray-500 text-sm">#324398</span>
                    </div>
                    <div class="text-xs text-gray-500 mb-4">07-05-2025, 03:19 pm</div>
                    <div class="text-xs text-gray-500">4 Items</div>
                </div>

                <!-- Order Card 2 -->
                <div class="min-w-[280px] bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span class="font-bold text-gray-900">Delivery</span>
                        </div>
                        <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-lg">Ready</span>
                    </div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-semibold text-gray-900 text-sm">Jung Kit</span>
                        <span class="text-gray-500 text-sm">#223399</span>
                    </div>
                    <div class="text-xs text-gray-500 mb-4">07-05-2025, 03:19 pm</div>
                    <div class="text-xs text-gray-500">6 Items</div>
                </div>

                <!-- Order Card 3 -->
                <div class="min-w-[280px] bg-white border border-gray-100 rounded-2xl p-4 shadow-sm opacity-60">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="font-bold text-gray-900">Dine in</span>
                        </div>
                        <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-lg">Canceled</span>
                    </div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-semibold text-gray-900 text-sm">John Pantau</span>
                        <span class="text-gray-500 text-sm">#4482...</span>
                    </div>
                    <div class="text-xs text-gray-500 mb-4">07-05-2025, 02:19 pm</div>
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>10 Items</span>
                        <span class="flex items-center gap-1 before:content-[''] before:w-1 before:h-1 before:bg-gray-300 before:rounded-full">Table 3A</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Section -->
        <div class="flex-1 flex flex-col min-h-0">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Menu List</h2>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-4.65a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                    </div>
                    <input type="text" placeholder="Search menu" class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 shadow-sm">
                </div>
            </div>

            <!-- Categories -->
            <div class="flex items-center gap-2 mb-6 bg-gray-100 p-1.5 rounded-2xl w-full">
                <button class="flex-1 px-4 py-2 text-sm font-medium text-gray-500 rounded-xl hover:text-gray-900 transition-colors">All</button>
                <button class="flex-1 px-4 py-2 text-sm font-medium text-gray-500 rounded-xl hover:text-gray-900 transition-colors">Appetizer</button>
                <button class="flex-1 px-4 py-2 text-sm font-medium text-gray-500 rounded-xl hover:text-gray-900 transition-colors">Main Dish</button>
                <button class="flex-1 px-4 py-2 text-sm font-semibold text-gray-900 bg-white rounded-xl shadow-sm transition-colors">Beverage</button>
                <button class="flex-1 px-4 py-2 text-sm font-medium text-gray-500 rounded-xl hover:text-gray-900 transition-colors">Snack</button>
                <button class="flex-1 px-4 py-2 text-sm font-medium text-gray-500 rounded-xl hover:text-gray-900 transition-colors">Dessert</button>
            </div>

            <!-- Menu Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pb-10 overflow-y-auto pr-2">
                @php
                    $menus = [
                        ['name' => 'Es Buah', 'price' => '26,000', 'color' => 'bg-pink-100'],
                        ['name' => 'Es Cincau', 'price' => '20,000', 'color' => 'bg-amber-100'],
                        ['name' => 'Es Cendol ljo', 'price' => '20,000', 'color' => 'bg-green-100'],
                        ['name' => 'Es Pisang ljo', 'price' => '25,000', 'color' => 'bg-pink-50'],
                        ['name' => 'Es Kelapa Muda', 'price' => '22,000', 'color' => 'bg-gray-100'],
                        ['name' => 'Es Teler', 'price' => '28,000', 'color' => 'bg-yellow-50'],
                    ];
                @endphp

                @foreach($menus as $menu)
                <div class="bg-white border border-gray-100 rounded-2xl p-3 shadow-sm flex flex-col">
                    <div class="h-32 {{ $menu['color'] }} rounded-xl mb-3 flex items-center justify-center relative overflow-hidden">
                       <!-- Placeholder for image -->
                       <div class="absolute inset-0 bg-gradient-to-tr from-black/5 to-transparent"></div>
                    </div>
                    <div class="font-bold text-gray-900 text-sm mb-1">{{ $menu['name'] }}</div>
                    <div class="font-semibold text-gray-900 text-sm mb-3">Rp. {{ $menu['price'] }}</div>
                    
                    <div class="flex gap-2 mb-4">
                        <select class="flex-1 text-xs border border-gray-200 rounded-lg py-1.5 px-2 bg-white text-gray-700 outline-none focus:border-blue-500">
                            <option>Regular</option>
                        </select>
                        <select class="flex-1 text-xs border border-gray-200 rounded-lg py-1.5 px-2 bg-white text-gray-700 outline-none focus:border-blue-500">
                            <option>Normal Sugar</option>
                        </select>
                    </div>
                    
                    <div class="mt-auto flex gap-2">
                        <div class="flex items-center justify-between border border-gray-200 rounded-xl px-2 w-20">
                            <button class="text-gray-500 hover:text-gray-900 p-1 w-6 h-6 flex items-center justify-center">-</button>
                            <span class="text-xs font-semibold px-1">0</span>
                            <button class="text-gray-500 hover:text-gray-900 p-1 w-6 h-6 flex items-center justify-center">+</button>
                        </div>
                        <button class="flex-1 flex items-center justify-center gap-1.5 border border-gray-200 rounded-xl py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add to cart
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column: Cart / Order Details -->
    <div class="w-[24rem] lg:w-[26rem] flex flex-col h-full overflow-hidden shrink-0">
        <div class="bg-white border text-gray-900 border-gray-100 rounded-2xl flex flex-col h-full shadow-sm">
            <div class="p-5 border-b border-gray-100 shrink-0">
                <h2 class="text-lg font-bold text-gray-900">Order Details</h2>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                <div class="p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Customer Information</h3>
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1.5">Customer name</label>
                            <input type="text" value="Jay Kowi" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900 font-medium">
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1.5">Order Type</label>
                                <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 cursor-pointer text-gray-900 font-medium bg-white">
                                    <option>Take Away</option>
                                    <option>Dine In</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1.5">Table number</label>
                                <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none cursor-not-allowed bg-gray-50 text-gray-400">
                                    <option>Select table</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-900">Order Items</h3>
                        <button class="text-xs text-red-500 font-medium hover:text-red-700 transition-colors">Reset Order</button>
                    </div>

                    <div class="space-y-4">
                        <!-- Cart Item 1 -->
                        <div class="flex gap-3">
                            <div class="w-16 h-16 bg-green-100 rounded-lg shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div class="font-semibold text-gray-900 text-sm truncate pr-2">Es Cendol ljo</div>
                                    <div class="flex gap-1 shrink-0">
                                        <button class="p-1 border border-red-100 rounded bg-red-50 text-red-500 hover:bg-red-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        <button class="p-1 border border-gray-200 rounded text-gray-500 hover:bg-gray-50">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">Variant : Regular</div>
                                <div class="text-xs text-gray-500">Sugar : Normal Sugar</div>
                                <div class="flex justify-between items-center mt-2">
                                    <div class="font-semibold text-sm text-gray-900">Rp. 20,000</div>
                                    <div class="text-sm font-medium text-gray-900">x1</div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Item 2 -->
                        <div class="flex gap-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div class="font-semibold text-gray-900 text-sm truncate pr-2">Es Kelapa Muda</div>
                                    <div class="flex gap-1 shrink-0">
                                        <button class="p-1 border border-red-100 rounded bg-red-50 text-red-500 hover:bg-red-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        <button class="p-1 border border-gray-200 rounded text-gray-500 hover:bg-gray-50">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">Variant : Regular</div>
                                <div class="text-xs text-gray-500">Sugar : Less Sugar</div>
                                <div class="flex justify-between items-center mt-2">
                                    <div class="font-semibold text-sm text-gray-900">Rp. 22,000</div>
                                    <div class="text-sm font-medium text-gray-900">x1</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="p-4 sm:p-5 border-t border-gray-100 bg-gray-50/50 shrink-0 rounded-b-2xl">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Payment Details</h3>
                
                <div class="mb-4">
                    <div class="relative w-full">
                        <select class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm appearance-none bg-white text-gray-900 font-medium cursor-pointer shadow-sm outline-none focus:border-blue-500">
                            <option>Cash</option>
                            <option>Credit Card</option>
                            <option>QRIS</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" transform="translate(2,0) scale(0.85)"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect><path d="M2" y="10" x="20"></path></svg>
                            <!-- use a money icon -->
                            <svg class="w-4 h-4 absolute inset-0 m-auto mt-0 ml-0 bg-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 mb-4">
                    <input type="text" placeholder="Promo Code" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 shadow-sm focus:outline-none focus:border-blue-500">
                    <button class="px-4 py-2 border border-blue-100 text-blue-600 font-medium text-sm rounded-lg hover:bg-blue-50 transition-colors bg-white shadow-sm">Apply</button>
                </div>

                <div class="space-y-2 mb-4 text-sm mt-5">
                    <div class="flex justify-between text-gray-500">
                        <span>Sub total</span>
                        <span class="font-bold text-gray-900">Rp. 290,000</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Discount(10%)</span>
                        <span class="font-bold text-gray-900">-Rp. 29,000</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Taxes(2%)</span>
                        <span class="font-bold text-gray-900">Rp. 5,700</span>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-5 pt-3 border-t border-gray-200 mt-5">
                    <span class="font-bold text-gray-900 text-base">Total</span>
                    <span class="font-bold text-[1.4rem] tracking-tight text-gray-900">Rp. 266,700</span>
                </div>

                <button class="w-full bg-[#113A6B] hover:bg-[#0C2A4E] text-white font-medium text-[0.95rem] rounded-[10px] py-[0.8rem] transition-colors shadow-sm focus:outline-none">
                    Confirm Payment
                </button>
            </div>
        </div>
    </div>
</div>
