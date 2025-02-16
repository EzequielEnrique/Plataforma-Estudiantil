import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { from } from 'rxjs';
import { LoginService } from 'src/app/login.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({

  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  form: FormGroup;
  isLoginMode: boolean = true;
  resLogin: any;
  loginError: boolean = false;
  submitted: boolean = false;
  resRegistro: any;
  registroError: any = '';
  errorMessage: any; 

  constructor(private loginservice: LoginService, 
              private fb: FormBuilder, 
              private router: Router,
              private _snackBar: MatSnackBar) {
    this.initLoginForm();
    this.form = this.fb.group({
      nombre: ['', Validators.required],
      apellido: [''],
      correo: ['',[Validators.required, Validators.email]],
      documento: [''],
      password: ['', Validators.required],
      repassword: ['']
    });
  }

  ngOnInit(): void {
  }

  ingresar() {
    const dni = this.form.value.documento;
    const password = this.form.value.password;
  
    if (this.isLoginMode) {
      
    this.submitted= true;
      const loginData: any = {
        perDni: dni,
        perContrasena: password
      };
  
      this.loginservice.login(loginData).subscribe(
        response => {
          this.resLogin = response;
          this.rolSelect(); // Llama a rolSelect después de recibir la respuesta
        },
        error => {
          this.loginError = true ,// Manejo de error, muestra un mensaje de error
          this.errorMessage = error.error.message;
          setTimeout(()=> {this.loginError = false;}, 3000);
          //console.error('Error en el inicio de sesión', error.error.message);
        }
      );
  } else {
      // Lógica de registro
      const nombre = this.form.value.nombre;
      const apellido = this.form.value.apellido;
      const email = this.form.value.correo;
      const documento = this.form.value.documento;
      const confirmPassword = this.form.value.repassword;

      const registroData: any = {
        perDni: documento,
        perContrasena: confirmPassword,
        perApellido: apellido,
        perNombre: nombre,
        perMail: email
      };

      if (this.form.valid && password === confirmPassword) {
        //logica de registro
        this.loginservice.register(registroData).subscribe(
          res=> {this.resRegistro = res;
            this._snackBar.open('Usuario generado con éxito. Inicia sesión', 'Cerrar', { duration: 5000 }); // Mensaje emergente
          },
          error => {
            this.registroError = error.message;
          }
          
        );
        this.toggleMode();
      }else{
        this.markAllFieldsAsTouched(); 
      }

     
    }
  }

  toggleMode() {
    this.isLoginMode = !this.isLoginMode;
    // Cambia las validaciones según el modo
  if (this.isLoginMode) {
    this.initLoginForm();  // Modo login, solo documento y password requeridos
  } else {
    this.initRegisterForm();  // Modo registro, todos los campos requeridos
  }
    console.log('Modo cambiado. Modo de inicio de sesión:', this.isLoginMode);
    this.form.reset(); // Reiniciar el formulario al cambiar de modo
  }

  rolSelect(){
    //Guardo el Token en el LocalStorage
    localStorage.setItem("Token", this.resLogin.Token);
    localStorage.setItem("Rol", this.resLogin.Rol);


    switch (this.resLogin.Rol){
      case "Estudiante": {
        //Ruta Estudiantes
        this.router.navigate(['./dashboard-estudiante']);
        break;
      }
      case "Administrador": {
        //Ruta Administrador
        //this.router.navigate(['./admin/listar-estudiantes']);
        break;
      }
      case "Bibliotecario": {
        //Ruta Preceptor
        this.router.navigate(['./dashboard-bibliotecario']);
        break;
      }
    }




  }
  passwordMatchValidator(form: FormGroup) {
    return form.get('password')?.value === form.get('repassword')?.value ? null : { mismatch: true };
  }

markAllFieldsAsTouched() {
  // Marcar todos los campos como tocados para activar los errores
  Object.keys(this.form.controls).forEach(field => {
    const control = this.form.get(field);
    control?.markAsTouched({ onlySelf: true });
  });
}
initLoginForm() {
  // Solo documento y contraseña son obligatorios en el login
  this.form = this.fb.group({
    documento: ['', Validators.required],
    password: ['', Validators.required],
    nombre: [''],
    apellido: [''],
    correo: [''],
    repassword: ['']
  });
}

initRegisterForm() {
  // Todos los campos son obligatorios en el registro
  this.form = this.fb.group({
    nombre: ['', Validators.required],
    apellido: ['', Validators.required],
    correo: ['', [Validators.required, Validators.email]],
    documento: ['', Validators.required],
    password: ['', Validators.required],
    repassword: ['', Validators.required]
  }, { validator: this.passwordMatchValidator }); // Agregar la validación personalizada
}
}


