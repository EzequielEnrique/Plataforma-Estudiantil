import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EditarDatosEstudianteComponent } from './editar-datos-estudiante.component';

describe('EditarDatosEstudianteComponent', () => {
  let component: EditarDatosEstudianteComponent;
  let fixture: ComponentFixture<EditarDatosEstudianteComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [EditarDatosEstudianteComponent]
    });
    fixture = TestBed.createComponent(EditarDatosEstudianteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
