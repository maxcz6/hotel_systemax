<nav>
    <div class="container">
        <div class="nav-content">
            <div style="display: flex; align-items: center;">
                <a href="{{ route('dashboard') }}" class="brand">{{ __('Hotel Systemax') }}</a>
                <ul>
                    <li><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    
                    @if(Auth::user()->role === 'recepcion' || Auth::user()->role === 'gerente')
                        <li><a href="{{ route('clientes.index') }}">{{ __('Clients') }}</a></li>
                        <li><a href="{{ route('habitaciones.index') }}">{{ __('Rooms') }}</a></li>
                        <li><a href="{{ route('reservas.index') }}">{{ __('Reservations') }}</a></li>
                        <li><a href="{{ route('pagos.index') }}">{{ __('Payments') }}</a></li>
                    @endif
                    
                    @if(Auth::user()->role === 'gerente')
                        <li><a href="{{ route('tipo_habitaciones.index') }}">{{ __('Room Types') }}</a></li>
                        <li><a href="{{ route('servicios.index') }}">{{ __('Services') }}</a></li>
                        <li><a href="{{ route('reportes.index') }}">{{ __('Reports') }}</a></li>
                    @endif

                    @if(Auth::user()->role === 'limpieza')
                        <li><a href="{{ route('limpieza.habitaciones') }}">{{ __('Rooms') }}</a></li>
                    @endif

                    @if(Auth::user()->role === 'mantenimiento')
                        <li><a href="{{ route('mantenimiento.habitaciones') }}">{{ __('Rooms') }}</a></li>
                    @endif
                </ul>
            </div>
            <div class="user-menu">
                <span>{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            style="margin-left: 10px; color: #ef4444;">
                        ({{ __('Log Out') }})
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
