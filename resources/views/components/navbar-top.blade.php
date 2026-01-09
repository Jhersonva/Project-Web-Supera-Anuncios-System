<nav class="navbar bg-white px-3 shadow-sm fixed-top d-flex justify-content-between">

    {{-- IZQUIERDA --}}
    <div class="d-flex align-items-center gap-2">
        <strong>{{ system_company_name() }}</strong>
    </div>

    {{-- DERECHA --}}
    <div class="d-flex align-items-center gap-3">

        @auth
        <div class="d-flex align-items-center gap-2">

            @php
                $isStaff = auth()->check() && in_array(auth()->user()->role_id, [1, 3]);
            @endphp

            @if($isStaff)

                {{-- CAMPANITA --}}
                <div class="dropdown">

                    <button
                        class="btn btn-light position-relative rounded-circle"
                        data-bs-toggle="dropdown"
                        style="width:42px;height:42px"
                    >
                        <i class="fa-solid fa-bell fs-5"></i>

                        @if($birthdays->count())
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                id="birthdayBadge"
                            >
                                {{ $birthdays->count() }}
                            </span>
                        @endif
                    </button>

                    <ul
                        class="dropdown-menu dropdown-menu-end shadow-lg p-0"
                        style="width:380px"
                        id="birthdayList"
                    >

                        <li class="dropdown-header text-center fw-bold bg-light">
                            🎉 Cumpleaños de hoy
                        </li>

                        <li class="dropdown-header text-warning fw-semibold">
                             <i class="fa-solid fa-hourglass-half"></i> Pendientes
                        </li>
                        <div id="pendingContainer"></div>

                        <li class="dropdown-divider"></li>

                        <li class="dropdown-header text-success fw-semibold">
                            <i class="fa-solid fa-paper-plane"></i> Enviados
                        </li>
                        <div id="sentContainer"></div>

                        @if(!$birthdays->count())
                            <li class="py-4 text-center text-muted small">
                                No hay cumpleaños hoy 🎂
                            </li>
                        @endif

                    </ul>
                </div>

                {{-- ITEMS (SOLO PARA STAFF) --}}
                @foreach($birthdays as $user)
                    <div class="birthday-item p-3 border-bottom" data-user-id="{{ $user->id }}">

                        <div class="fw-semibold fs-6">
                            {{ $user->full_name ?? $user->company_reason }}
                        </div>

                        <div class="text-muted small">
                            Tiene promoción especial 🎁
                        </div>

                        @if($user->whatsapp)
                            <a
                                href="https://wa.me/51{{ $user->whatsapp }}"
                                target="_blank"
                                class="btn btn-success btn-sm w-100 mt-2 btn-whatsapp"
                                data-user-id="{{ $user->id }}"
                            >
                                <i class="fa-brands fa-whatsapp me-1"></i>
                                Enviar WhatsApp
                            </a>
                        @else
                            <div class="text-danger small mt-2">
                                Sin número de WhatsApp
                            </div>
                        @endif
                    </div>
                @endforeach

            @endif

            <img
                src="{{ auth()->user()->profile_image
                    ? asset(auth()->user()->profile_image)
                    : asset('assets/img/profile-image/default-user.png') }}"
                class="rounded-circle border border-2 border-danger"
                style="width:32px; height:32px; object-fit:cover;"
                alt="Perfil">

            <span class="fw-semibold">
                @if(auth()->user()->account_type === 'business')
                    {{ auth()->user()->company_reason }}
                @else
                    {{ auth()->user()->full_name }}
                @endif
            </span>

            <form action="{{ route('auth.logout') }}" method="POST" class="ms-2">
                @csrf
                <button class="btn btn-danger btn-sm">
                    <i class="fa-solid fa-right-to-bracket me-1"></i>Salir
                </button>
            </form>
        </div>

        @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-right-to-bracket me-2"></i>Ingresar
            </a>
        @endauth

    </div>
</nav>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const SENT_KEY = 'birthday_sent_users';
    let sentUsers = JSON.parse(sessionStorage.getItem(SENT_KEY) || '[]');

    const badge = document.getElementById('birthdayBadge');
    const pendingContainer = document.getElementById('pendingContainer');
    const sentContainer = document.getElementById('sentContainer');

    function render() {
        const items = document.querySelectorAll('.birthday-item');
        pendingContainer.innerHTML = '';
        sentContainer.innerHTML = '';

        let pendingCount = 0;

        items.forEach(item => {
            const userId = item.dataset.userId;
            const btn = item.querySelector('.btn-whatsapp');

            if (sentUsers.includes(userId)) {
                if (btn) {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-secondary');
                    btn.innerHTML = '✔ Enviado';
                    btn.classList.add('disabled');
                }
                sentContainer.appendChild(item);
            } else {
                pendingContainer.appendChild(item);
                pendingCount++;
            }
        });

        // Badge
        if (badge) {
            if (pendingCount > 0) {
                badge.textContent = pendingCount;
            } else {
                badge.remove();
            }
        }
    }

    document.querySelectorAll('.btn-whatsapp').forEach(btn => {
        btn.addEventListener('click', () => {
            const userId = btn.dataset.userId;

            if (!sentUsers.includes(userId)) {
                sentUsers.push(userId);
                sessionStorage.setItem(SENT_KEY, JSON.stringify(sentUsers));
            }

            setTimeout(render, 300);
        });
    });

    render();
});
</script>
