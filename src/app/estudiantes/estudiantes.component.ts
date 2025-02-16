import { Component, ElementRef, ViewChild, OnInit } from '@angular/core';
import { InscripcionesService } from '../inscripciones.service';
import { DataService } from '../data.service';
import { ActivatedRoute } from '@angular/router';
import * as jspdf from 'jspdf';
import html2canvas from 'html2canvas';
import { jsPDF } from 'jspdf';
import { LoginService } from '../login.service';
import { Router } from '@angular/router';


@Component({
  selector: 'app-estudiantes',
  templateUrl: './estudiantes.component.html',
  styleUrls: ['./estudiantes.component.css']
})
export class EstudiantesComponent implements OnInit {

  inscripciones: any[] = [];
  searchTerm: string = '';
  dni: string = "";

  constructor( 
    private route: ActivatedRoute,
    private dataService: DataService,
    private loginService: LoginService,
    private router: Router) { }

  ngOnInit(): void {
    if (this.loginService.isAuthenticate() == false){
      localStorage.removeItem('Token');
      localStorage.removeItem('Rol');
      this.router.navigate(['/login']);
    }

    this.searchDNI()
  }

  searchDNI(): void {
    this.inscripciones = this.dataService.getInscripciones();


    if(this.dataService.getCargado() == false){
      this.router.navigate(['/dashboard-estudiante']);
    }
  }
  


 //ACA EMPIEZA LA CONFIGURACION DE JSPDF
 @ViewChild('content', { static: true }) content!: ElementRef;

 generarPDF(){
  const pdf = new jsPDF({
   orientation: 'portrait',
   unit: 'mm',
   format: 'a4'
 });

   const content = this.content.nativeElement;

   pdf.html(content, {
     callback: (pdf) => {
       
       const nombrepdf='inscripcion.pdf';
       pdf.save(nombrepdf);
     },
     x: 5,
     y: 5,
     html2canvas: {
       scale: 0.25 // Escala para ajustar al ancho de la página A4
     }
   });
 }
  

}
