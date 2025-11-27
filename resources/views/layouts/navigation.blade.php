<nav>
    <div class="container">
        <div class="nav-content">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="brand">Hotel Systemax</a>
                <ul>
                    <li><a href="{{ route('dashboard') }}">Panel</a></li>
                    
                    @if(in_array(Auth::user()->role, ['recepcion', 'gerente', 'administrador']))
                        <li><a href="{{ route('clientes.index') }}">Clientes</a></li>
                        <li><a href="{{ route('habitaciones.index') }}">Habitaciones</a></li>
                        <li><a href="{{ route('reservas.index') }}">Reservas</a></li>
                        <li><a href="{{ route('pagos.index') }}">Pagos</a></li>
                    @endif
                    
                    @if(in_array(Auth::user()->role, ['gerente', 'administrador']))
                        <li><a href="{{ route('tipo_habitaciones.index') }}">Tipos</a></li>
                        <li><a href="{{ route('servicios.index') }}">Servicios</a></li>
                        <li><a href="{{ route('mantenimientos.index') }}">Mantenimiento</a></li>
                        <li><a href="{{ route('limpieza.habitaciones') }}">Limpieza</a></li>
                        <li><a href="{{ route('reportes.index') }}">Reportes</a></li>
                    @endif

                    @if(Auth::user()->role === 'limpieza')
                        <li><a href="{{ route('limpieza.habitaciones') }}">Habitaciones</a></li>
                    @endif

                    @if(Auth::user()->role === 'mantenimiento')
                        <li><a href="{{ route('mantenimiento.habitaciones') }}">Tareas</a></li>
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
                            style="color: #ef4444;">
                        Salir
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
