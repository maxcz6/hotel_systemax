<nav>
    <div class="container">
        <div class="nav-content">
            <div style="display: flex; align-items: center;">
                <a href="{{ route('dashboard') }}" class="brand">Hotel System</a>
                <ul>
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    
                    @if(Auth::user()->role === 'recepcion' || Auth::user()->role === 'gerente')
                        <li><a href="{{ route('clientes.index') }}">Clientes</a></li>
                        <li><a href="{{ route('habitaciones.index') }}">Habitaciones</a></li>
                        <li><a href="{{ route('reservas.index') }}">Reservas</a></li>
                        <li><a href="{{ route('pagos.index') }}">Pagos</a></li>
                    @endif
                    
                    @if(Auth::user()->role === 'gerente')
                        <li><a href="{{ route('tipo_habitaciones.index') }}">Tipos de Habitación</a></li>
                        <li><a href="{{ route('servicios.index') }}">Servicios</a></li>
                        <li><a href="{{ route('reportes.index') }}">Reportes</a></li>
                    @endif

                    @if(Auth::user()->role === 'limpieza')
                        <li><a href="{{ route('limpieza.habitaciones') }}">Habitaciones</a></li>
                    @endif

                    @if(Auth::user()->role === 'mantenimiento')
                        <li><a href="{{ route('mantenimiento.habitaciones') }}">Habitaciones</a></li>
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
                        (Logout)
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
