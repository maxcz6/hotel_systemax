<x-app-layout>
    <div class="container">
        <div class="page-header">
            <h1>Detalles de la Reserva #{{ $reserva->id }}</h1>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver a la lista</a>
                
                @if(in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion']) && !in_array($reserva->estado, ['cancelada', 'checkout']))
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('modal-cancelar-reserva').style.display='block'">
                        Cancelar Reserva
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <h3>Información de la Reserva</h3>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 1rem;">
                    <div>
                        <strong>Cliente:</strong> {{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}
                    </div>
                    <div>
                        <strong>Email:</strong> {{ $reserva->cliente->email ?? 'No especificado' }}
                    </div>
                    <div>
                        <strong>Teléfono:</strong> {{ $reserva->cliente->telefono ?? 'No especificado' }}
                    </div>
                    <div>
                        <strong>Habitación:</strong> #{{ $reserva->habitacion->numero }} 
                        @if($reserva->habitacion->tipoHabitacion)
                            - {{ $reserva->habitacion->tipoHabitacion->nombre }}
                        @endif
                    </div>
                    <div>
                        <strong>Fecha de Entrada:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}
                    </div>
                    <div>
                        <strong>Fecha de Salida:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}
                    </div>
                    <div>
                        <strong>Precio Habitación:</strong> ${{ number_format($reserva->total_precio, 2) }}
                    </div>
                    <div>
                        <strong>Estado:</strong> 
                        @if($reserva->estado === 'pendiente')
                            <span style="color: #f59e0b; font-weight: 600;">Pendiente</span>
                        @elseif($reserva->estado === 'confirmada')
                            <span style="color: #059669; font-weight: 600;">Confirmada</span>
                        @elseif($reserva->estado === 'checkin')
                            <span style="color: #3b82f6; font-weight: 600;">Check-in Realizado</span>
                        @elseif($reserva->estado === 'checkout')
                            <span style="color: #6b7280; font-weight: 600;">Check-out Realizado</span>
                        @else
                            <span style="color: #dc2626; font-weight: 600;">{{ ucfirst($reserva->estado) }}</span>
                        @endif
                    </div>
                </div>

                @if($reserva->notas)
                    <div style="margin-top: 1rem; padding: 1rem; background-color: #fef3c7; border-left: 4px solid #f59e0b;">
                        <strong>Notas:</strong> {{ $reserva->notas }}
                    </div>
                @endif

                @if($reserva->estado === 'cancelada')
                    <div style="margin-top: 1rem; padding: 1rem; background-color: #fee2e2; border-left: 4px solid #ef4444;">
                        <strong>🚫 Reserva Cancelada</strong><br>
                        <strong>Fecha:</strong> {{ $reserva->fecha_cancelacion ? $reserva->fecha_cancelacion->format('d/m/Y H:i') : 'N/A' }}<br>
                        <strong>Motivo:</strong> {{ $reserva->motivo_cancelacion }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Sección de Servicios Consumidos (solo si hay check-in) --}}
        @if($reserva->estancia)
            <div class="card" style="margin-top: 2rem;">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3>Servicios Consumidos</h3>
                        @if(in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion']) && $reserva->estado === 'checkin')
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('modal-agregar-servicio').style.display='block'">
                                + Agregar Servicio
                            </button>
                        @endif
                    </div>

                    @php
                        $serviciosDetalle = $reserva->estancia->serviciosDetalle ?? collect([]);
                        // Solo sumar servicios activos
                        $totalServicios = $serviciosDetalle->where('estado', '!=', 'anulado')->sum('subtotal');
                    @endphp

                    @if($serviciosDetalle->count() > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Estado</th>
                                    @if(in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion']) && $reserva->estado === 'checkin')
                                        <th>Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviciosDetalle as $detalle)
                                    <tr style="{{ $detalle->estado === 'anulado' ? 'background-color: #fee2e2; color: #991b1b;' : '' }}">
                                        <td>
                                            {{ $detalle->servicio->nombre ?? 'N/A' }}
                                            @if($detalle->estado === 'anulado')
                                                <br><small>Motivo: {{ $detalle->motivo_anulacion }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $detalle->cantidad }}</td>
                                        <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td style="{{ $detalle->estado === 'anulado' ? 'text-decoration: line-through;' : '' }}">
                                            ${{ number_format($detalle->subtotal, 2) }}
                                        </td>
                                        <td>
                                            @if($detalle->estado === 'anulado')
                                                <span style="color: #dc2626; font-weight: bold;">ANULADO</span>
                                            @else
                                                <span style="color: #059669;">Activo</span>
                                            @endif
                                        </td>
                                        @if(in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion']) && $reserva->estado === 'checkin')
                                            <td>
                                                @if($detalle->estado !== 'anulado')
                                                    {{-- Botón Anular (Admin, Gerente, Recepción) --}}
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="abrirModalAnular({{ $detalle->id }})"
                                                            style="background-color: #f59e0b; border-color: #f59e0b;">
                                                        Anular
                                                    </button>
                                                @endif

                                                {{-- Botón Eliminar Físicamente (SOLO ADMIN) --}}
                                                @if(Auth::user()->role === 'administrador')
                                                    <form action="{{ route('servicio_detalle.destroy', $detalle) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar PERMANENTEMENTE este servicio? Esta acción no se puede deshacer.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" style="margin-left: 5px;">Eliminar</button>
                                                    </form>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="font-weight: 600; background-color: #f3f4f6;">
                                    <td colspan="3" style="text-align: right;">Total Servicios Activos:</td>
                                    <td colspan="{{ in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion']) && $reserva->estado === 'checkin' ? '4' : '3' }}">${{ number_format($totalServicios, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        <div style="margin-top: 1.5rem; padding: 1rem; background-color: #eff6ff; border-left: 4px solid #3b82f6;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div>
                                    <strong>Subtotal Habitación:</strong> ${{ number_format($reserva->total_precio, 2) }}
                                </div>
                                <div>
                                    <strong>Subtotal Servicios:</strong> ${{ number_format($totalServicios, 2) }}
                                </div>
                            </div>
                            <div style="margin-top: 0.5rem; font-size: 1.25rem; font-weight: 700; color: #1e40af;">
                                <strong>TOTAL GENERAL:</strong> ${{ number_format($reserva->total_precio + $totalServicios, 2) }}
                            </div>
                        </div>
                    @else
                        <p style="text-align: center; color: #6b7280; padding: 2rem;">No se han registrado servicios para esta estadía.</p>
                    @endif
                </div>
            </div>

            {{-- Modal Agregar Servicio --}}
            @if(in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion']) && $reserva->estado === 'checkin')
                <div id="modal-agregar-servicio" class="modal" style="display: none;">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('servicio_detalle.store') }}">
                            @csrf
                            <input type="hidden" name="id_estancia" value="{{ $reserva->estancia->id }}">

                            <div class="modal-header">
                                <h2>Agregar Servicio</h2>
                                <button type="button" class="close" onclick="document.getElementById('modal-agregar-servicio').style.display='none'">&times;</button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="id_servicio">Servicio</label>
                                    <select name="id_servicio" id="id_servicio" class="form-control" required>
                                        <option value="">Seleccione un servicio</option>
                                        @foreach(\App\Models\Servicio::all() as $servicio)
                                            <option value="{{ $servicio->id }}" data-precio="{{ $servicio->precio }}">
                                                {{ $servicio->nombre }} - ${{ number_format($servicio->precio, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="cantidad">Cantidad</label>
                                    <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" value="1" required>
                                </div>

                                <div id="preview-total" style="margin-top: 1rem; padding: 1rem; background-color: #f3f4f6; border-radius: 0.375rem; display: none;">
                                    <strong>Total estimado:</strong> $<span id="total-preview">0.00</span>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-agregar-servicio').style.display='none'">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Agregar Servicio</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Modal Anular Servicio --}}
                <div id="modal-anular-servicio" class="modal" style="display: none;">
                    <div class="modal-content">
                        <form id="form-anular-servicio" method="POST" action="">
                            @csrf
                            @method('PUT')
                            
                            <div class="modal-header">
                                <h2>Anular Servicio</h2>
                                <button type="button" class="close" onclick="document.getElementById('modal-anular-servicio').style.display='none'">&times;</button>
                            </div>

                            <div class="modal-body">
                                <div class="alert alert-danger">
                                    ¿Está seguro de anular este servicio? Esta acción restará el monto del total.
                                </div>
                                <div class="form-group">
                                    <label for="motivo_anulacion">Motivo de Anulación</label>
                                    <textarea name="motivo_anulacion" id="motivo_anulacion" class="form-control" rows="3" required placeholder="Ej: Error al cargar, cliente canceló pedido..."></textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-anular-servicio').style.display='none'">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Confirmar Anulación</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    // Calcular total en tiempo real
                    const servicioSelect = document.getElementById('id_servicio');
                    const cantidadInput = document.getElementById('cantidad');
                    const previewDiv = document.getElementById('preview-total');
                    const totalSpan = document.getElementById('total-preview');

                    function calcularTotal() {
                        const selectedOption = servicioSelect.options[servicioSelect.selectedIndex];
                        const precio = parseFloat(selectedOption.getAttribute('data-precio')) || 0;
                        const cantidad = parseInt(cantidadInput.value) || 0;
                        const total = precio * cantidad;

                        if (precio > 0 && cantidad > 0) {
                            totalSpan.textContent = total.toFixed(2);
                            previewDiv.style.display = 'block';
                        } else {
                            previewDiv.style.display = 'none';
                        }
                    }

                    servicioSelect.addEventListener('change', calcularTotal);
                    cantidadInput.addEventListener('input', calcularTotal);

                    // Función para abrir modal de anulación
                    function abrirModalAnular(id) {
                        const form = document.getElementById('form-anular-servicio');
                        form.action = `/servicio_detalle/${id}/anular`;
                        document.getElementById('modal-anular-servicio').style.display = 'block';
                    }

                    // Cerrar modales al hacer clic fuera
                    window.onclick = function(event) {
                        const modalAgregar = document.getElementById('modal-agregar-servicio');
                        const modalAnular = document.getElementById('modal-anular-servicio');
                        const modalCancelarReserva = document.getElementById('modal-cancelar-reserva');
                        
                        if (event.target == modalAgregar) modalAgregar.style.display = 'none';
                        if (event.target == modalAnular) modalAnular.style.display = 'none';
                        if (event.target == modalCancelarReserva) modalCancelarReserva.style.display = 'none';
                    }
                </script>
            @endif
        @else
            <div class="card" style="margin-top: 2rem;">
                <div class="card-body">
                    <h3>Servicios Consumidos</h3>
                    <div style="padding: 2rem; text-align: center; color: #6b7280;">
                        <p>⚠️ No se puede agregar servicios porque aún no se ha realizado el check-in para esta reserva.</p>
                        <p style="margin-top: 0.5rem; font-size: 0.875rem;">El cliente debe hacer check-in primero antes de poder registrar servicios consumidos.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Cancelar Reserva --}}
        @if(in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion']) && !in_array($reserva->estado, ['cancelada', 'checkout']))
            <div id="modal-cancelar-reserva" class="modal" style="display: none;">
                <div class="modal-content">
                    <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="modal-header">
                            <h2>Cancelar Reserva</h2>
                            <button type="button" class="close" onclick="document.getElementById('modal-cancelar-reserva').style.display='none'">&times;</button>
                        </div>

                        <div class="modal-body">
                            <div class="alert alert-danger">
                                ¿Está seguro de cancelar esta reserva? Esta acción liberará la habitación.
                            </div>
                            <div class="form-group">
                                <label for="motivo_cancelacion">Motivo de Cancelación</label>
                                <textarea name="motivo_cancelacion" id="motivo_cancelacion" class="form-control" rows="3" required placeholder="Ej: Cliente no se presentó, cancelación telefónica..."></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-cancelar-reserva').style.display='none'">Cerrar</button>
                            <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Sección de Pagos --}}
        @if($reserva->pagos && $reserva->pagos->count() > 0)
            <div class="card" style="margin-top: 2rem;">
                <div class="card-body">
                    <h3>Pagos Registrados</h3>
                    <table class="table" style="margin-top: 1rem;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reserva->pagos as $pago)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}</td>
                                    <td>${{ number_format($pago->monto, 2) }}</td>
                                    <td>{{ ucfirst($pago->metodo_pago) }}</td>
                                    <td>
                                        @if($pago->estado === 'completado')
                                            <span style="color: #059669;">Completado</span>
                                        @elseif($pago->estado === 'pendiente')
                                            <span style="color: #f59e0b;">Pendiente</span>
                                        @else
                                            <span style="color: #dc2626;">Anulado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
