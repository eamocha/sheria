import React from 'react';
import ReactDOM from 'react-dom';
import APPageHeader from './APPageHeader';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APPageHeader />, div);
  ReactDOM.unmountComponentAtNode(div);
});