@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-2xl shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">ប្រព័ន្ធរក្សាទុក និងស្ដារទិន្នន័យ (Backup & Restore)</h2>
            <p class="text-sm text-gray-500">ចម្លងទុកទិន្នន័យរបស់ប្រព័ន្ធ ឬស្ដារឡើងវិញរាល់ពេលមានបញ្ហា</p>
        </div>

        <!-- ប៊ូតុងបង្កើត Backup ថ្មី (ជាមួយ Loading) -->
        <form action="{{ route('backups.create') }}" method="POST" onsubmit="showLoadingModal('កំពុងរក្សាទុកទិន្នន័យ (Backup)... សូមរង់ចាំ')">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition flex items-center gap-2">
                <span>💾 បង្កើត Backup ថ្មី</span>
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-3 text-sm font-semibold text-gray-600">ឈ្មោះឯកសារ (.sql)</th>
                    <th class="p-3 text-sm font-semibold text-gray-600">ទំហំឯកសារ</th>
                    <th class="p-3 text-sm font-semibold text-gray-600">កាលបរិច្ឆេទបង្កើត</th>
                    <th class="p-3 text-sm font-semibold text-gray-600 text-right">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3 text-sm font-semibold text-gray-800">
                            {{ $backup['name'] }}
                        </td>
                        <td class="p-3 text-sm text-gray-600">{{ $backup['size'] }}</td>
                        <td class="p-3 text-sm text-gray-600">{{ $backup['date'] }}</td>
                        <td class="p-3 text-sm text-right flex justify-end space-x-2">
                            <!-- ទាញយក -->
                            <a href="{{ route('backups.download', $backup['name']) }}" class="px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-bold rounded transition">
                                ទាញយក
                            </a>

                            <!-- ប៊ូតុងស្ដារទិន្នន័យ (ហៅតាម Popup Modal) -->
                            <button type="button" onclick="triggerRestore('{{ route('backups.restore', $backup['name']) }}')" class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-800 text-xs font-bold rounded transition">
                                ស្ដារឡើងវិញ
                            </button>

                            <!-- ប៊ូតុងលុបចោល (ហៅតាម Popup Modal) -->
                            <button type="button" onclick="triggerDelete('{{ route('backups.destroy', $backup['name']) }}')" class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-800 text-xs font-bold rounded transition">
                                លុបចោល
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-500">មិនទាន់មានឯកសារចម្លងទុក (Backup) ឡើយ។</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- ១. POPUP MODAL សម្រាប់បញ្ជាក់ការយល់ព្រម (Confirmation Modal) -->
<!-- ========================================== -->
<div id="confirm-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- ផ្ទាំងខាងក្រោយងងឹត (Backdrop) -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeConfirmModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- ផ្ទាំង Panel របស់ Modal -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div id="modal-icon-bg" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <!-- Icon ព្រមាន -->
                        <svg class="h-6 w-6" id="modal-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            ចំណងជើងផ្ទាំងបញ្ជាក់
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="modal-message">
                                សារបញ្ជាក់លម្អិត...
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form សម្រាប់បញ្ជូនការបញ្ជា -->
            <form id="modal-form" method="POST">
                @csrf
                <input type="hidden" name="_method" id="modal-method" value="POST">

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" id="modal-submit-btn" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 text-base font-semibold text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        យល់ព្រម
                    </button>
                    <button type="button" onclick="closeConfirmModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        បដិសេធ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- ២. POPUP MODAL សម្រាប់បង្ហាញដំណើរការវិល (Loading Spinner Modal) -->
<!-- ========================================== -->
<div id="loading-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 text-center">
        <!-- ផ្ទាំងខាងក្រោយងងឹត (Backdrop) -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

        <div class="inline-block bg-white rounded-2xl p-6 text-center shadow-xl transform transition-all sm:max-w-sm sm:w-full">
            <div class="flex flex-col items-center justify-center space-y-4">
                <!-- កង់វិល (Spinner) -->
                <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-semibold text-gray-700" id="loading-text">កំពុងដំណើរការ... សូមរង់ចាំ</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    /**
     * បើកទម្រង់ Modal បញ្ជាក់ (Confirm Modal)
     */
    function openConfirmModal(actionUrl, method, title, message, isWarning = true, btnText = 'យល់ព្រម') {
        const modal = document.getElementById('confirm-modal');
        const form = document.getElementById('modal-form');
        const methodInput = document.getElementById('modal-method');
        const titleEl = document.getElementById('modal-title');
        const messageEl = document.getElementById('modal-message');
        const submitBtn = document.getElementById('modal-submit-btn');
        const iconBg = document.getElementById('modal-icon-bg');
        const icon = document.getElementById('modal-icon');

        form.action = actionUrl;
        methodInput.value = method;
        titleEl.textContent = title;
        messageEl.textContent = message;
        submitBtn.textContent = btnText;

        // កំណត់ទម្រង់ និងពណ៌ទៅតាមប្រភេទនៃសកម្មភាព (លុប ឬ ស្ដារឡើងវិញ)
        if (isWarning) {
            // ពណ៌ក្រហម (សម្រាប់លុប)
            iconBg.className = "mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10";
            icon.className = "h-6 w-6 text-red-600";
            submitBtn.className = "w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-semibold text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition";
        } else {
            // ពណ៌បៃតង (សម្រាប់ស្ដារទិន្នន័យ)
            iconBg.className = "mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10";
            icon.className = "h-6 w-6 text-green-600";
            submitBtn.className = "w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-semibold text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition";
        }

        // បង្ហាញ Modal
        modal.classList.remove('hidden');

        // នៅពេលចុះឈ្មោះ Submit Form ឱ្យដំណើរការ Loading Spinner
        form.onsubmit = function() {
            let loadingMsg = isWarning ? 'កំពុងលុបឯកសារ... សូមរង់ចាំ' : 'កំពុងស្ដារទិន្នន័យ (Restore Database)... សូមរង់ចាំ';
            showLoadingModal(loadingMsg);
        };
    }

    /**
     * បិទ Modal បញ្ជាក់
     */
    function closeConfirmModal() {
        document.getElementById('confirm-modal').classList.add('hidden');
    }

    /**
     * បង្ហាញផ្ទាំង Loading Spinner
     */
    function showLoadingModal(text = 'កំពុងដំណើរការ... សូមរង់ចាំ') {
        closeConfirmModal(); // បិទប្រអប់ Confirm សិន
        document.getElementById('loading-text').textContent = text;
        document.getElementById('loading-modal').classList.remove('hidden');
    }

    /**
     * ហៅប្រអប់ស្ដារទិន្នន័យ (Restore Database Popup)
     */
    function triggerRestore(url) {
        openConfirmModal(
            url,
            'POST',
            '🔄 ស្ដារទិន្នន័យឡើងវិញ (Restore Database)',
            'ប្រយ័ត្ន៖ ការស្ដារទិន្នន័យនេះ នឹងជំនួសទិន្នន័យបច្ចុប្បន្នទាំងអស់! រាល់ការលក់ និងផលិតផលថ្មីៗបន្ទាប់ពីកាលបរិច្ឆេទចម្លងទុក នឹងត្រូវបាត់បង់ទាំងស្រុង។ តើអ្នកពិតជាចង់ស្ដារមែនទេ?',
            false, // មិនមែន Warning ពណ៌ក្រហមទេ (ពណ៌បៃតង)
            'ស្ដារឡើងវិញ'
        );
    }

    /**
     * ហៅប្រអប់លុបឯកសារចម្លងទុក (Delete Backup Popup)
     */
    function triggerDelete(url) {
        openConfirmModal(
            url,
            'DELETE',
            '🗑️ លុបឯកសារចម្លងទុក (Delete Backup)',
            'តើអ្នកពិតជាចង់លុបឯកសារ Backup នេះចេញពីប្រព័ន្ធមែនទេ? អ្នកមិនអាចទាញយកឯកសារនេះត្រឡប់មកវិញបានឡើយ។',
            true, // បង្ហាញពណ៌ក្រហម (ព្រមាន)
            'លុបចោល'
        );
    }
</script>
@endpush
