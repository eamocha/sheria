import React from 'react';
import ReactDOM from 'react-dom';
import APAutocompleteList from './APAutocompleteList';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APAutocompleteList />, div);
  ReactDOM.unmountComponentAtNode(div);
});