import React from 'react';
import ReactDOM from 'react-dom';
import ChangeForm from './ChangeForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ChangeForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});