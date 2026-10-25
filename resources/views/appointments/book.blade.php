<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Consultation | Venture Visionary Qatar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Top Header -->
    <header class="bg-[#0F2942] text-white py-4 shadow-md border-b border-amber-500/30">
        <div class="max-w-5xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-500 flex items-center justify-center font-bold text-slate-900 text-lg shadow-sm">
                    VV
                </div>
                <div>
                    <h1 class="font-bold text-base tracking-tight leading-none text-white">Venture Visionary</h1>
                    <p class="text-xs text-amber-400 font-medium tracking-wide">Visa & Business Advisory • Doha, Qatar</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <span class="text-xs text-slate-300 block">Need Urgent Assistance?</span>
                <span class="text-xs font-semibold text-amber-400">WhatsApp: +974 5555 1234</span>
            </div>
        </div>
    </header>

    <!-- Main Booking Section -->
    <main class="max-w-4xl mx-auto px-4 py-8 flex-1 w-full">
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 mb-2">
                ⚡ Instant Calendar Scheduling
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0F2942] tracking-tight">
                Schedule Your 1-on-1 Visa Consultation
            </h2>
            <p class="text-sm text-slate-600 mt-1 max-w-xl mx-auto">
                Select your intended travel destination and reserve a confirmed time slot with one of our licensed Doha visa advisors.
            </p>
        </div>

        @if(session('booking_success'))
            @php $res = session('booking_success'); @endphp
            <div class="bg-emerald-50 border-2 border-emerald-500 rounded-2xl p-6 text-center max-w-lg mx-auto shadow-sm my-6">
                <div class="w-14 h-14 bg-emerald-500 text-white rounded-full flex items-center justify-center text-2xl mx-auto mb-3 shadow">
                    ✓
                </div>
                <h3 class="text-xl font-bold text-emerald-950">Appointment Confirmed!</h3>
                <p class="text-sm text-emerald-800 mt-2">
                    Your consultation reference is <strong class="font-mono text-emerald-950">{{ $res['lead_number'] }}</strong>.
                </p>
                <div class="bg-white rounded-xl p-4 mt-4 border border-emerald-200 text-left text-xs space-y-1.5">
                    <p><strong>Scheduled Time:</strong> {{ $res['time'] }} (Qatar Standard Time)</p>
                    <p><strong>Assigned Consultant:</strong> {{ $res['consultant'] }}</p>
                    <p><strong>Location:</strong> Venture Visionary Executive Office / Google Meet link will be messaged to you on WhatsApp.</p>
                </div>
                <div class="mt-6 flex justify-center gap-3">
                    <a href="/" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition">
                        Back to Home
                    </a>
                    <a href="/book-appointment" class="px-4 py-2 border border-emerald-300 text-emerald-800 rounded-lg text-xs font-semibold hover:bg-emerald-100/50 transition">
                        Book Another
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <form action="/book-appointment" method="POST" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <!-- Step 1: Destination & Service -->
                    <div>
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-amber-500 text-slate-900 flex items-center justify-center text-xs font-black">1</span>
                            Service & Destination
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Target Destination Country *</label>
                                <select name="destination_country" required
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none bg-slate-50">
                                    <option value="">Select country...</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->name }}">{{ $c->name }}</option>
                                    @endforeach
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="Schengen Area">Schengen Area (Europe)</option>
                                    <option value="Canada">Canada</option>
                                    <option value="United States">United States</option>
                                    <option value="Qatar Business Setup">Qatar LLC Company Formation</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Visa or Service Category *</label>
                                <select name="service_type" required
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none bg-slate-50">
                                    <option value="Tourist & Visitor Visa">Tourist / Visit Visa</option>
                                    <option value="Business & Investment Visa">Business & Commercial Visa</option>
                                    <option value="Family & Resident Sponsorship">Family Sponsorship / Residence</option>
                                    <option value="Student & Study Visa">Student / University Visa</option>
                                    <option value="Corporate LLC Setup">Qatar Corporate Business Setup</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Date & Slot Picker -->
                    <div class="border-t border-slate-100 pt-6">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-amber-500 text-slate-900 flex items-center justify-center text-xs font-black">2</span>
                            Pick Appointment Slot
                        </h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Select Consultation Date *</label>
                                <input type="date" id="bookingDateInput" min="{{ now()->addDay()->toDateString() }}" value="{{ now()->addDay()->toDateString() }}"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none bg-slate-50" />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Available Time Slots (Qatar Standard Time) *</label>
                                <div id="timeSlotsContainer" class="grid grid-cols-3 gap-2">
                                    <!-- Dynamic chips filled via JS -->
                                    <div class="col-span-3 text-xs text-slate-400 py-2">Loading slots...</div>
                                </div>
                                <input type="hidden" name="appointment_time" id="selectedAppointmentTime" required />
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Contact Info -->
                    <div class="border-t border-slate-100 pt-6">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-amber-500 text-slate-900 flex items-center justify-center text-xs font-black">3</span>
                            Your Contact Details
                        </h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Legal Name *</label>
                                <input type="text" name="name" required placeholder="e.g. Mohammed Al-Kuwari"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none bg-slate-50" />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">WhatsApp / Phone *</label>
                                <input type="tel" name="phone" required placeholder="+974 5555 1234"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none bg-slate-50" />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                                <input type="email" name="email" placeholder="client@example.com"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none bg-slate-50" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Special Requests or Application Details (Optional)</label>
                            <textarea name="notes" rows="2" placeholder="Briefly describe your current travel timeline or Qatar residency status..."
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none bg-slate-50"></textarea>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-6 flex items-center justify-between">
                        <span class="text-xs text-slate-500">🔒 Free Consultation • No Credit Card Required</span>
                        <button type="submit" id="submitBtn" disabled
                            class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-50 disabled:cursor-not-allowed text-slate-900 font-bold text-sm shadow transition">
                            Confirm Appointment Booking →
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-6 text-xs text-center border-t border-slate-800">
        <p>© 2026 Venture Visionary Visa and Business Consultancy, Doha, Qatar. All rights reserved.</p>
    </footer>

    <script>
        const dateInput = document.getElementById('bookingDateInput');
        const container = document.getElementById('timeSlotsContainer');
        const hiddenInput = document.getElementById('selectedAppointmentTime');
        const submitBtn = document.getElementById('submitBtn');

        function loadSlots(dateStr) {
            container.innerHTML = '<div class="col-span-3 text-xs text-slate-400 py-2">Finding available times...</div>';
            fetch('/api/v1/appointments/available-slots?date=' + encodeURIComponent(dateStr))
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = '';
                    if (!data.slots || data.slots.length === 0) {
                        container.innerHTML = '<div class="col-span-3 text-xs text-rose-500 py-2">No available slots on this date.</div>';
                        return;
                    }
                    data.slots.forEach(slot => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = `p-2 rounded-lg text-xs font-semibold border transition ${
                            slot.available 
                                ? 'border-slate-300 hover:border-amber-500 hover:bg-amber-50 text-slate-800 bg-white' 
                                : 'border-slate-100 text-slate-300 bg-slate-50 cursor-not-allowed'
                        }`;
                        btn.innerText = slot.display;
                        if (slot.available) {
                            btn.onclick = () => {
                                document.querySelectorAll('#timeSlotsContainer button').forEach(b => {
                                    b.classList.remove('bg-amber-500', 'text-slate-900', 'border-amber-500');
                                    b.classList.add('bg-white', 'text-slate-800');
                                });
                                btn.classList.remove('bg-white', 'text-slate-800');
                                btn.classList.add('bg-amber-500', 'text-slate-900', 'border-amber-500');
                                hiddenInput.value = slot.datetime;
                                submitBtn.disabled = false;
                            };
                        } else {
                            btn.disabled = true;
                        }
                        container.appendChild(btn);
                    });
                })
                .catch(() => {
                    container.innerHTML = '<div class="col-span-3 text-xs text-rose-500 py-2">Failed to load slots.</div>';
                });
        }

        if (dateInput) {
            dateInput.addEventListener('change', (e) => loadSlots(e.target.value));
            loadSlots(dateInput.value);
        }
    </script>
</body>
</html>
