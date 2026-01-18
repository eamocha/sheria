import React from 'react';
import ReactDOM from 'react-dom';
import APCheckboxBtn from './APCheckboxBtn';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APCheckboxBtn />, div);
  ReactDOM.unmountComponentAtNode(div);
});