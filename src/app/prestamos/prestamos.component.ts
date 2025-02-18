import { Component, OnInit } from '@angular/core';
import { PrestamoService } from '../prestamo.service';
import { AuthService } from '../auth.service'; 

@Component({
  selector: 'app-prestamos',
  templateUrl: './prestamos.component.html',
  styleUrls: ['./prestamos.component.css'],
})
export class PrestamosComponent implements OnInit {
  prestamos: any[] = [];
  isStudent: boolean = false; 
  prestamoSeleccionado: any = {
    nombre: '',
    apellido: '',
    dni: '',
    libro: '',
    fecha_prestamo: '',
    fecha_devolucion: '',
  };
  modalVisible: boolean = false;
  editando: boolean = false;

  constructor(private prestamoService: PrestamoService, private authService: AuthService) {}

  ngOnInit(): void {
    this.cargarPrestamos(); 
    this.verificarRol(); 
  }

  verificarRol(): void {
    const rol = localStorage.getItem('Rol');
    this.isStudent = rol === 'Estudiante'; 
  }

  cargarPrestamos(): void {
    this.prestamoService.getPrestamos().subscribe((data: any[]) => {
      this.prestamos = data;
    });
  }

  abrirModal(prestamo: any = null): void {
    if (this.isStudent) return; 
    this.modalVisible = true;
    this.editando = !!prestamo;

    if (prestamo) {
      this.prestamoSeleccionado = { ...prestamo };
    } else {
      this.prestamoSeleccionado = {
        nombre: '',
        apellido: '',
        dni: '',
        libro: '',
        fecha_prestamo: '',
        fecha_devolucion: '',
      };
    }
  }

  cerrarModal(): void {
    this.modalVisible = false;
  }

  guardarPrestamo(): void {
    if (this.isStudent) return; 
    if (this.editando) {
      this.prestamoService
        .updatePrestamo(this.prestamoSeleccionado.id, this.prestamoSeleccionado)
        .subscribe(() => {
          this.cargarPrestamos();
          this.cerrarModal();
        });
    } else {
      this.prestamoService.createPrestamo(this.prestamoSeleccionado).subscribe(() => {
        this.cargarPrestamos();
        this.cerrarModal();
      });
    }
  }

  eliminarPrestamo(id: number): void {
    if (this.isStudent) return; 
    this.prestamoService.deletePrestamo(id).subscribe(() => {
      this.cargarPrestamos();
    });
  }

  editarPrestamo(prestamo: any): void {
    if (this.isStudent) return; 
    this.abrirModal(prestamo);
  }
}












