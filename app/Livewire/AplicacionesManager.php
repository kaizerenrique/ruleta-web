<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Aplicacion;

class AplicacionesManager extends Component
{
    // Estado del modal de crear/editar
    public $mostrarModal = false;
    public $editandoId = null;

    // Campos del formulario
    public $nombre = '';
    public $descripcion = '';
    public $activo = true;

    // Estado del modal de confirmación de eliminación
    public $confirmandoEliminarId = null;

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string|max:255',
        'activo' => 'boolean',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre de la aplicación es obligatorio.',
        'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
        'descripcion.max' => 'La descripción no puede superar los 255 caracteres.',
    ];

    /**
     * Abrir modal en modo "crear" (formulario vacío).
     */
    public function abrirModal()
    {
        $this->reset(['editandoId', 'nombre', 'descripcion']);
        $this->activo = true;
        $this->resetValidation();
        $this->mostrarModal = true;
    }

    /**
     * Cerrar modal sin guardar.
     */
    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->resetValidation();
    }

    /**
     * Abrir modal en modo "editar" con datos precargados.
     */
    public function editar($id)
    {
        $app = Aplicacion::find($id);
        if (!$app) return;

        $this->editandoId = $app->id;
        $this->nombre = $app->nombre;
        $this->descripcion = $app->descripcion;
        $this->activo = $app->activo;
        $this->resetValidation();
        $this->mostrarModal = true;
    }

    /**
     * Guardar (crear o actualizar según el estado).
     */
    public function guardar()
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion ?: null,
            'activo' => $this->activo,
        ];

        if ($this->editandoId) {
            Aplicacion::find($this->editandoId)?->update($data);
            session()->flash('success', 'Aplicación actualizada correctamente.');
        } else {
            Aplicacion::create($data);
            session()->flash('success', 'Aplicación creada correctamente.');
        }

        $this->cerrarModal();
    }

    /**
     * Abrir modal de confirmación de eliminación.
     */
    public function confirmarEliminar($id)
    {
        $this->confirmandoEliminarId = $id;
    }

    /**
     * Cancelar eliminación.
     */
    public function cancelarEliminar()
    {
        $this->confirmandoEliminarId = null;
    }

    /**
     * Eliminar la aplicación confirmada.
     */
    public function eliminar()
    {
        if ($this->confirmandoEliminarId) {
            Aplicacion::find($this->confirmandoEliminarId)?->delete();
            session()->flash('success', 'Aplicación eliminada correctamente.');
        }
        $this->confirmandoEliminarId = null;
    }

    public function render()
    {
        return view('livewire.aplicaciones-manager', [
            'aplicaciones' => Aplicacion::orderBy('nombre')->get(),
        ]);
    }
}
