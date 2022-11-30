@extends("redis-manager::layout")

@section("scripts")
<script type="text/babel">
  const { useState, useEffect } = React;
  const {
      colors,
      CssBaseline,
      ThemeProvider,
      Typography,
      Container,
      createTheme,
      Box,
      SvgIcon,
      Link,
      AppBar,
      Toolbar,
      Drawer,
      List,
      ListItem ,
      ListItemButton,
      ListItemIcon,
      ListItemText, 
      Divider,
      Paper,
      TableContainer,
      Table,
      TableHead,
      TableRow,
      TableCell,
      TableBody,
      TablePagination,
      Button,
      ButtonGroup,
      Alert,
      Dialog,
      DialogContent
  } = MaterialUI;
console.log(MaterialUI)

  // Create a theme instance.
  const theme = createTheme({
  palette: {
    primary: {
    main: "#121212",
    },
    secondary: {
    main: "#19857b",
    },
    error: {
    main: colors.red.A400,
    },
  },
  });

  const drawerWidth = 350;
  const toolbarHeight = 56;
  
  const queryPing = () => fetch("/api/redis-manager/ping");
  const fetchAllFolder = () => fetch("/api/redis-manager/all-folder");
  const fetchFolderColumn = (folderName) => fetch(`/api/redis-manager/folder-column/${folderName}`);
  const fetchFolderData = (folderName) => fetch(`/api/redis-manager/folder-data/${folderName}`);
  const fetchFolderDataList = (folderName, params = {}) => fetch(`/api/redis-manager/folder-data-list/${folderName}?${new URLSearchParams(params)}`,);

  const flushFolder = (folderName) => fetch(`/api/redis-manager/flush-folder/${folderName}`, { method: 'DELETE'});
  const flushAllFolder = (folderName) => fetch("/api/redis-manager/flush-all", { method: 'DELETE'});

  const RootProvider = React.createContext({});

  const FolderIcon = (props) => {
        return (
          <SvgIcon {...props}>
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"></path>
          </SvgIcon>
        );
      }
    
  const ReloadIcon = (props) => {
    return (
      <SvgIcon {...props}>
      <path d="m19 8-4 4h3c0 3.31-2.69 6-6 6-1.01 0-1.97-.25-2.8-.7l-1.46 1.46C8.97 19.54 10.43 20 12 20c4.42 0 8-3.58 8-8h3l-4-4zM6 12c0-3.31 2.69-6 6-6 1.01 0 1.97.25 2.8.7l1.46-1.46C15.03 4.46 13.57 4 12 4c-4.42 0-8 3.58-8 8H1l4 4 4-4H6z"></path>
      </SvgIcon>
    );
  }
  
  const DeleteIcon = (props) => {
    return (
      <SvgIcon {...props}>
        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zm2.46-7.12 1.41-1.41L12 12.59l2.12-2.12 1.41 1.41L13.41 14l2.12 2.12-1.41 1.41L12 15.41l-2.12 2.12-1.41-1.41L10.59 14l-2.13-2.12zM15.5 4l-1-1h-5l-1 1H5v2h14V4z"></path>
      </SvgIcon>
    );
  }
  
  const HomeIcon = (props) => {
    return (
      <SvgIcon {...props}>
        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path>
      </SvgIcon>
    );
  }


  const HomeApp = (props) => {
    return (
      <h1>Hello Hoooman</h1>
    )
  }

  const MainApp = (props) => {
    const { folder } = props;
    const [ folderColumn, setFolderColumn ] = useState({});
    const [ folderData, setFolderData] = useState([]);
    const [ dataTotal, setDataTotal] = useState(0);
    const [ dataModel, setDataModel ] = useState({
      page: 0,
      limit: 15
    });

    const { setGlobalLoading } = React.useContext(RootProvider);

    const queryFolder = async () => {
      setGlobalLoading(true);
      const resultFolderColumn = await fetchFolderColumn(folder);
      const folderColumnData =  await resultFolderColumn.json();
      let data = [];
      let totalData = 0;

      if (resultFolderColumn.ok) {
        const resultFolderData = await fetchFolderDataList(folder, dataModel);
        const folderResultData= await resultFolderData.json();
        data = folderResultData.data || [];
        totalData = folderResultData.total || 0;
      }
      setDataTotal(totalData);
      setFolderData(data);
      setFolderColumn(folderColumnData);
      setGlobalLoading(false);
    }

    const handleDataModel = (params) => setDataModel(prev => ({...prev, ...params}));

    const handleChangePage = (event, newPage) => handleDataModel({page: newPage});
    const handleChangeRowsPerPage = (event, newPage) => handleDataModel({limit: parseInt(event.target.value, 10)});
    const handleOnReloadFolder = (event) => setDataModel({limit: 15, page: 0});

    useEffect(() => {
      if(!folder) return;
      queryFolder();
    }, [folder, dataModel]);

    return (
      <Box display='flex' flexDirection='column' gap={2}>
        {
          folder && (
            <Toolbar component={Paper}>
            <Typography variant="h4" component="div" sx=@{{ flexGrow: 1 }}>
              {folder || ''}
            </Typography>
            <Button onClick={handleOnReloadFolder} color="success" variant="contained" startIcon={<ReloadIcon/>}>Reload</Button>
          </Toolbar>
          )
        }
        {
          folder ? (
            <TableContainer component={Paper}>
              <Table sx=@{{ minWidth: 650 }} aria-label="simple table">
                <TableHead>
                  <TableRow>
                    {
                      Object.entries(folderColumn).map((data, key) => <TableCell key={`table-folder-column-${key}`}><strong>{data[0] || ''} ({data[1] || ''})</strong></TableCell>)
                    }
                  </TableRow>
                </TableHead>
                <TableBody>
                  {
                    folderData.map((row, key) => (
                      <TableRow key={`table-folder-container-${key}`}>
                      {
                        Object.keys(folderColumn).map((item, key) => <TableCell key={`table-folder-column-${key}`}>{JSON.stringify(row[item] || 'NULL')}</TableCell>)
                      }
                    </TableRow>))
                  }
                </TableBody>
              </Table>
              <TablePagination
                component="div"
                count={dataTotal}
                page={dataModel.page}
                onPageChange={handleChangePage}
                rowsPerPage={dataModel.limit}
                onRowsPerPageChange={handleChangeRowsPerPage}
                rowsPerPageOptions={[15, 100, 1000]}
              />
            </TableContainer>
          ) : (
            <HomeApp />
          )
        }
      </Box>
    );
  }

  const Pinger = ({children, handleStatus}) => {
    const [ pinging, setPinging ] = useState(false);
    const [ isErrorShownOnce, setIsErrorShownOnce ] = useState(false);

    useEffect(() => {
      const pingInterval = setTimeout(async () => {
        if(pinging) return;
        setPinging(true);
        const result = await queryPing();
        const data = await result.json();
        if(!result.ok) {
          if(!isErrorShownOnce) {
            setIsErrorShownOnce(true);
          }
          clearInterval(pingInterval);
        } else {
          setIsErrorShownOnce(false);
        }

        setPinging(false);

        if (typeof handleStatus === 'function') handleStatus(result.ok);

      }, 4000);

      return () => {
        clearInterval(pingInterval);
      };
    });

    return (<Box>{children}</Box>);
  }

  const GlobalDialog = ({ open, children }) => {
    return (
      <Dialog disableEscapeKeyDown={true} open={open}>
        <DialogContent>
          { children }      
        </DialogContent>
      </Dialog>
      );
  }

  const BaseProvider = (props) => {
    const {
      onFolderSelected, children,
      online = true
    } = props;
    const [ allFolderData, setAllFolderData ] = useState([]);
    const [ selectedFolder, setSelectedFolder ] = useState('');

    const { setGlobalLoading } = React.useContext(RootProvider);

    const queryAllFolder = async () => {
      setGlobalLoading(true);
      const result = await fetchAllFolder();
      const { data= [] } = await result.json();
      setAllFolderData(data);
      setGlobalLoading(false);
    };

    const handleOnFolderSelected = (folderValue, e) => {
      setSelectedFolder(folderValue);
      if (typeof onFolderSelected === 'function') onFolderSelected(folderValue);
    };

    useEffect(() => {
      queryAllFolder();
    }, []);

    return (
      <Box sx=@{{ display: 'flex' }}>
        <CssBaseline />
        <AppBar position="fixed" sx=@{{ zIndex: (theme) => theme.zIndex.drawer + 1 }}>
        <Toolbar>
          <Typography variant="h6" noWrap component="div">
              Laravel Redis Manager | { Boolean(online) ? 'O N L I N E' : 'O F F L I N E'}
          </Typography>
          </Toolbar>
        </AppBar>
        <Drawer
          variant="permanent"
          sx=@{{
            width: drawerWidth,
            flexShrink: 0,
            [`& .MuiDrawer-paper`]: { width: drawerWidth, boxSizing: 'border-box' },
          }}
        >
        <Toolbar/>
        <Toolbar>
        <ButtonGroup variant="outlined" aria-label="outlined primary button group">
          <Button onClick={(event) => handleOnFolderSelected(null, event)} size="small" color="info" variant="contained" startIcon={<HomeIcon/>}>Home</Button>
          <Button onClick={() => console.log('yes')} size="small" color="error" variant="contained" startIcon={<DeleteIcon/>}>Drop</Button>
          <Button onClick={() => queryAllFolder()} color="success" variant="contained" startIcon={<ReloadIcon/>}>Reload</Button>
        </ButtonGroup>
        </Toolbar>
          <Box sx=@{{ overflow: 'auto', padding: 1 }}>
          <List>
            {allFolderData.map((data, index) => (
              <ListItem key={index} disablePadding selected={selectedFolder === data}>
                <ListItemButton onClick={(e) => handleOnFolderSelected(data, e)}>
                  <ListItemIcon>
                    <FolderIcon/>
                  </ListItemIcon>
                  <ListItemText primary={data} />
                  </ListItemButton>
                  <Button onClick={() => console.log('yes')} size="small" color="error" variant="contained" startIcon={<DeleteIcon/>}>Remove</Button>
              </ListItem>
            ))}
          </List>
        </Box>
        </Drawer>
          <Box
          component='main'
          sx=@{{
            flexGrow: 1,
            height: '100vh',
            overflow: 'auto',
            }}
          >
          <Toolbar />
          <Box p={2} pt={0}>
          { children }
          </Box>
        </Box>
      </Box>
    )
  }

  const App = () => {
    const [ folder, setFolder ] = useState(null);
    const [ status, setStatus ] = useState(true);
    const [ globalLoading, setGlobalLoading ] = useState(true);

    return (
      <RootProvider.Provider value=@{{ globalLoading, setGlobalLoading }}>
        <ThemeProvider theme={theme}>
          <Pinger handleStatus={(statusValue) => setStatus(statusValue)}>
            <GlobalDialog open={!Boolean(status) || globalLoading}>
              {
                Boolean(globalLoading) ? 
                (
                  <Alert severity="info"><strong>Please wait...</strong></Alert>
                ) : (
                  !Boolean(status) && (<Alert severity="error"><strong>No Redis connection!!!</strong></Alert>)
                )
              }
            </GlobalDialog>
            <BaseProvider onFolderSelected={(folderValue) => setFolder(folderValue)} online={status}>
              <MainApp folder={folder}/>
            </BaseProvider>
          </Pinger>
      </ThemeProvider>
      </RootProvider.Provider>
    )
  }

  const root = ReactDOM.createRoot(document.getElementById("root"));
  root.render(<App />);
</script>
@endsection
