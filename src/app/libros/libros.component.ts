import { Component, OnInit } from '@angular/core';
import { LibrosService } from '../libros.service';

@Component({
  selector: 'app-libros',
  templateUrl: './libros.component.html',
  styleUrls: ['./libros.component.css'],
})
export class LibrosComponent implements OnInit {
  libros: any[] = [];
  librosFiltrados: any[] = [];
  librosPaginados: any[] = [];

  currentLibro: any = { id: null, nombre: '', autor: '', fecha_publicacion: '', genero: '', sinopsis: '' };

  showForm: boolean = false;
  editMode: boolean = false;

  paginaActual: number = 1;
  itemsPorPagina: number = 6;
  totalPaginas: number = 0;
  terminoBusqueda: string = '';

  constructor(private librosService: LibrosService) {}

  ngOnInit(): void {
    this.obtenerLibros();
  }

  obtenerLibros(): void {
    this.librosService.getLibros().subscribe((data: any[]) => {
      this.libros = data;
      this.librosFiltrados = data;
      this.actualizarPaginacion();
    });
  }

  buscarLibro(): void {
    if (this.terminoBusqueda.trim()) {
      this.librosFiltrados = this.libros.filter((libro) =>
        libro.nombre.toLowerCase().includes(this.terminoBusqueda.toLowerCase())
      );
    } else {
      this.librosFiltrados = [...this.libros];
    }
    this.actualizarPaginacion();
  }

  limpiarBusqueda(): void {
    this.terminoBusqueda = '';
    this.librosFiltrados = [...this.libros];
    this.actualizarPaginacion();
  }

  actualizarPaginacion(): void {
    this.totalPaginas = Math.ceil(this.librosFiltrados.length / this.itemsPorPagina);
    this.actualizarLibrosPaginados();
  }

  cambiarPagina(pagina: number): void {
    if (pagina >= 1 && pagina <= this.totalPaginas) {
      this.paginaActual = pagina;
      this.actualizarLibrosPaginados();
    }
  }

  actualizarLibrosPaginados(): void {
    const inicio = (this.paginaActual - 1) * this.itemsPorPagina;
    const fin = inicio + this.itemsPorPagina;
    this.librosPaginados = this.librosFiltrados.slice(inicio, fin);
  }

  obtenerPaginasAdyacentes(): number[] {
    const inicio = Math.max(1, this.paginaActual - 1);
    const fin = Math.min(this.totalPaginas, this.paginaActual + 1);
    return Array.from({ length: fin - inicio + 1 }, (_, i) => inicio + i);
  }

  mostrarFormulario(): void {
    this.showForm = true;
    this.editMode = false;
    this.currentLibro = { id: null, nombre: '', autor: '', fecha_publicacion: '', genero: '', sinopsis: '' };
  }

  cancelar(): void {
    this.showForm = false;
    this.editMode = false;
  }

  crearLibro(): void {
    this.librosService.createLibro(this.currentLibro).subscribe(() => {
      this.obtenerLibros();
      this.cancelar();
    });
  }

  editarLibro(libro: any): void {
    this.currentLibro = { ...libro };
    this.showForm = true;
    this.editMode = true;
  }

  actualizarLibro(): void {
    this.librosService.updateLibro(this.currentLibro).subscribe(() => {
      this.obtenerLibros();
      this.cancelar();
    });
  }

  eliminarLibro(id: number): void {
    this.librosService.deleteLibro(id).subscribe(() => {
      this.obtenerLibros();
    });
  }
}

