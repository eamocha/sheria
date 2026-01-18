import React from 'react';
import ReactDOM from 'react-dom';
import AdvisorTaskAddForm from './AdvisorTaskAddForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AdvisorTaskAddForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});