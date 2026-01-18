import React from 'react';
import ReactDOM from 'react-dom';
import GeneralInfoItem from './GeneralInfoItem';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<GeneralInfoItem />, div);
  ReactDOM.unmountComponentAtNode(div);
});