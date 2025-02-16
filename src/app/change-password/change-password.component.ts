import { Component } from '@angular/core';
import { ServiciosUsuariosService } from 'src/app/servicios-usuarios.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Router } from '@angular/router';
import { Location } from '@angular/common';


@Component({
  selector: 'app-change-password',
  templateUrl: './change-password.component.html',
  styleUrls: ['./change-password.component.css']
})
export class ChangePasswordComponent {
  nuevaContrasena: string = '';
  confirmarContrasena: string = '';

  constructor(
    private usService: ServiciosUsuariosService,
    private snackBar: MatSnackBar,
    private router: Router,
    private location: Location 

  ) {}

  cambiarContrasena() {
    if (this.nuevaContrasena !== this.confirmarContrasena) {
      this.snackBar.open('Las contraseñas no coinciden', 'Cerrar', {
        duration: 3000,
      });
      return;
    }

    const usuarioId = this.usService.getUsuarioData().perId;

    // Llamar al servicio para actualizar la contraseña
    this.usService.cambiarContrasena(usuarioId, this.nuevaContrasena).subscribe(
      () => {
        this.snackBar.open('Contraseña actualizada exitosamente', 'Cerrar', {
          duration: 3000,
        });
  
        // Obtener el rol del usuario
        const rol = localStorage.getItem('Rol'); 
  
        // Redirigir al dashboard correspondiente según el rol
        if (rol === 'Estudiante') {
          this.router.navigate(['/dashboard-estudiante']);
        } else if (rol === 'Bibliotecario') {
          this.router.navigate(['/dashboard-bibliotecario']);
        } else {
          this.router.navigate(['/']); // Redirigir al inicio si el rol no es válido
        }
      },
      (error) => {
        console.error('Error al cambiar la contraseña', error);
        this.snackBar.open('Error al actualizar la contraseña', 'Cerrar', {
          duration: 3000,
        });
      }
    );
  }

  cancelar() {
    this.location.back(); // Regresa a la página anterior
  }
}

