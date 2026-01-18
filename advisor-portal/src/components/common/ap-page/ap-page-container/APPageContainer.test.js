import React from 'react';
import ReactDOM from 'react-dom';
import APPageContainer from './APPageContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APPageContainer />, div);
  ReactDOM.unmountComponentAtNode(div);
});