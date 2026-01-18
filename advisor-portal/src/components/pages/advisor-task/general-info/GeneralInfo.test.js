import React from 'react';
import ReactDOM from 'react-dom';
import GeneralInfo from './GeneralInfo';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<GeneralInfo />, div);
  ReactDOM.unmountComponentAtNode(div);
});