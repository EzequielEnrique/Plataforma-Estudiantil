import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AutoresEditarComponent } from './autores-editar.component';

describe('AutoresEditarComponent', () => {
  let component: AutoresEditarComponent;
  let fixture: ComponentFixture<AutoresEditarComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AutoresEditarComponent]
    });
    fixture = TestBed.createComponent(AutoresEditarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
