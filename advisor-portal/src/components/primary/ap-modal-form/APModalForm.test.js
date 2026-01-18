import React from 'react';
import ReactDOM from 'react-dom';
import APModalForm from './APModalForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APModalForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});