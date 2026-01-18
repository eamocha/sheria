import React from 'react';
import ReactDOM from 'react-dom';
import Attachments from './Attachments';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<Attachments />, div);
  ReactDOM.unmountComponentAtNode(div);
});