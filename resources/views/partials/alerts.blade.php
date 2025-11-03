<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-3">
    @php
        $messages = [
            'success' => session('success'),
            'error' => session('error'),
            'warning' => session('warning'),
            'info' => session('info'),
        ];
        $hasErrors = isset($errors) && $errors->any();
    @endphp

    @foreach($messages as $type => $message)
        @if($message)
            <div class="toast {{ $type }} shadow-lg rounded-lg px-4 py-3 flex items-start text-sm"
                 role="alert" aria-live="assertive" aria-atomic="true">
                <div class="mr-3 mt-0.5">
                    @if($type === 'success')
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-check"></i>
                        </span>
                    @elseif($type === 'error')
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-times"></i>
                        </span>
                    @elseif($type === 'warning')
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-exclamation"></i>
                        </span>
                    @else
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-info"></i>
                        </span>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="font-semibold mb-0.5 text-gray-900">{{ ucfirst($type) }}</p>
                    <p class="text-gray-700">{{ $message }}</p>
                </div>
                <button type="button" class="ml-3 text-gray-400 hover:text-gray-600" aria-label="Cerrar" onclick="dismissToast(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
    @endforeach

    @if($hasErrors)
        <div class="toast error shadow-lg rounded-lg px-4 py-3 flex items-start text-sm" role="alert">
            <div class="mr-3 mt-0.5">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times"></i>
                </span>
            </div>
            <div class="flex-1">
                <p class="font-semibold mb-0.5 text-gray-900">Errores de validación</p>
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="ml-3 text-gray-400 hover:text-gray-600" aria-label="Cerrar" onclick="dismissToast(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
</div>

<style>
    .toast.success { background: #ecfdf5; border: 1px solid #6ee7b7; }
    .toast.error { background: #fee2e2; border: 1px solid #fca5a5; }
    .toast.warning { background: #fef3c7; border: 1px solid #fcd34d; }
    .toast.info { background: #eff6ff; border: 1px solid #93c5fd; }
    .toast { animation: slideIn 0.25s ease-out; }
    @keyframes slideIn {
        from { transform: translateY(-6px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

<script>
    function dismissToast(btn){
        const toast = btn.closest('.toast');
        if(!toast) return;
        toast.style.transition = 'opacity 150ms ease';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 180);
    }
    // Auto-dismiss toasts after 5s
    window.addEventListener('DOMContentLoaded', () => {
        const toasts = document.querySelectorAll('#toast-container .toast');
        toasts.forEach(t => {
            setTimeout(() => {
                if(document.body.contains(t)){
                    t.style.transition = 'opacity 150ms ease';
                    t.style.opacity = '0';
                    setTimeout(() => t.remove(), 180);
                }
            }, 5000);
        });
    });

    // Helper global para mostrar toasts dinámicos sin redirección
    // Uso: window.showToast('success'|'error'|'warning'|'info', 'Mensaje')
    window.showToast = function(type, message){
        const icons = {
            success: '<i class="fas fa-check"></i>',
            error: '<i class="fas fa-times"></i>',
            warning: '<i class="fas fa-exclamation"></i>',
            info: '<i class="fas fa-info"></i>'
        };
        const titles = {
            success: 'Éxito',
            error: 'Error',
            warning: 'Aviso',
            info: 'Info'
        };
        const container = document.getElementById('toast-container');
        if(!container){
            const div = document.createElement('div');
            div.id = 'toast-container';
            div.className = 'fixed top-4 right-4 z-50 space-y-3';
            document.body.appendChild(div);
        }
        const c = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type} shadow-lg rounded-lg px-4 py-3 flex items-start text-sm`;
        toast.setAttribute('role','alert');
        toast.setAttribute('aria-live','assertive');
        toast.setAttribute('aria-atomic','true');
        toast.innerHTML = `
            <div class="mr-3 mt-0.5">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full ${type==='success'?'bg-green-100 text-green-600': type==='error'?'bg-red-100 text-red-600': type==='warning'?'bg-yellow-100 text-yellow-600':'bg-blue-100 text-blue-600'}">
                    ${icons[type]||icons.info}
                </span>
            </div>
            <div class="flex-1">
                <p class="font-semibold mb-0.5 text-gray-900">${titles[type]||titles.info}</p>
                <p class="text-gray-700">${message}</p>
            </div>
            <button type="button" class="ml-3 text-gray-400 hover:text-gray-600" aria-label="Cerrar">\n                <i class="fas fa-times"></i>\n            </button>
        `;
        const closeBtn = toast.querySelector('button');
        closeBtn.addEventListener('click', () => dismissToast(closeBtn));
        c.appendChild(toast);
        // auto-dismiss
        setTimeout(() => dismissToast(closeBtn), 5000);
    }

    // Helper para envío AJAX de formularios con toasts
    // Marca el formulario con data-ajax="true" y opcionalmente:
    // - data-success-message="Texto éxito"
    // - data-error-message="Texto error genérico"
    // - data-reset-on-success="true" para limpiar el formulario
    // Si el servidor responde JSON con { message, redirect }, se usa y redirige opcionalmente.
    window.bindAjaxForms = function(){
        const forms = document.querySelectorAll('form[data-ajax="true"]');
        forms.forEach(form => {
            if(form.__ajaxBound) return; // evitar doble binding
            form.__ajaxBound = true;
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                const originalText = submitBtn && submitBtn.innerHTML;
                if(submitBtn){
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50');
                    submitBtn.innerHTML = `<span class="inline-flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i>Procesando...</span>`;
                }

                try{
                    const fd = new FormData(form);
                    // Soporte method spoofing de Laravel
                    const methodSpoof = (fd.get('_method')||'').toString().toUpperCase();
                    const method = methodSpoof || (form.getAttribute('method')||'POST').toUpperCase();
                    const action = form.getAttribute('action')||window.location.href;

                    const res = await fetch(action, {
                        method: method,
                        body: fd,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    const ct = res.headers.get('content-type')||'';
                    let data = null;
                    if(ct.includes('application/json')){
                        data = await res.json().catch(() => null);
                    } else {
                        // Mejor esfuerzo: HTML/redirect estándar
                        await res.text().catch(() => null);
                    }

                    if(res.ok){
                        const msg = (data && (data.message||data.msg)) || form.getAttribute('data-success-message') || 'Operación realizada correctamente.';
                        window.showToast('success', msg);
                        const reset = (form.getAttribute('data-reset-on-success')||'').toLowerCase() === 'true';
                        if(reset) form.reset();
                        if(data && data.redirect){
                            setTimeout(() => { window.location.href = data.redirect; }, 600);
                        }
                    } else {
                        let errMsg = form.getAttribute('data-error-message') || 'No se pudo completar la operación.';
                        // Muestra errores de validación si vienen en JSON
                        if(data && (data.errors || data.error)){
                            const errors = data.errors || { general: [data.error] };
                            const list = Object.values(errors).flat().join('\n');
                            errMsg = `Error:\n${list}`;
                        }
                        window.showToast('error', errMsg.replace(/\n/g,'<br>'));
                    }
                } catch(err){
                    window.showToast('error', 'Error de red o servidor. Intenta nuevamente.');
                } finally {
                    if(submitBtn){
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50');
                        submitBtn.innerHTML = originalText;
                    }
                }
            });
        });
    }

    // Auto-bind al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        try { window.bindAjaxForms(); } catch(e) { /* noop */ }
    });
</script>