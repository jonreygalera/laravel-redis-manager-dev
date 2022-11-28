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
      Icon,
      Paper,
      TableContainer,
      Table,
      TableHead,
      TableRow,
      TableCell,
      TableBody
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

  const drawerWidth = 300;
  const toolbarHeight = 56;
  
  const queryPing = () => fetch("/api/redis-manager/ping");
  const fetchAllFolder = () => fetch("/api/redis-manager/all-folder");
  const fetchFolderColumn = (folderName) => fetch(`/api/redis-manager/folder-column/${folderName}`);
  const fetchFolderData = (folderName) => fetch(`/api/redis-manager/folder-data/${folderName}`);
  const flushFolder = (folderName) => fetch(`/api/redis-manager/flush-folder/${folderName}`, { method: 'DELETE'});
  const flushAllFolder = (folderName) => fetch("/api/redis-manager/flush-all", { method: 'DELETE'});

  const FolderIcon = (props) => {
        return (
          <SvgIcon {...props}>
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"></path>
          </SvgIcon>
        );
      }

  const MainApp = (props) => {
    const { folder } = props;
    const [ folderColumn, setFolderColumn ] = useState({});
    const [ folderData, setFolderData] = useState([]);
    const [ loading, setLoading] = useState(false);

    const queryFolder = async () => {
      setLoading(true);
      const resultFolderColumn = await fetchFolderColumn(folder);
      const folderColumnData =  await resultFolderColumn.json();

      if (resultFolderColumn.ok) {
        const resultFolderData = await fetchFolderData(folder);
        const folderResultData= await resultFolderData.json();
        setFolderData(folderResultData);
      }
      setFolderColumn(folderColumnData);
      setLoading(false);

    }

    useEffect(() => {
      if(!folder) return;
      queryFolder();
    }, [folder]);

    return (
      <Box>
        <TableContainer component={Paper}>
          <Table sx=@{{ minWidth: 650 }} aria-label="simple table">
            <TableHead>
              <TableRow>
                {
                  Object.entries(folderColumn).map((data, key) => <TableCell key={`table-folder-item${key}`}>{data[0] || ''} ({data[1] || ''})</TableCell>)
                }
              </TableRow>
            </TableHead>
            <TableBody>
              
            </TableBody>
          </Table>
        </TableContainer>
      </Box>
    );
  }

  const Pinger = () => {
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
            alert(data.message || "No redis connection.");
            setIsErrorShownOnce(true);
          }
          clearInterval(pingInterval);
        } else {
          setIsErrorShownOnce(false);
        }

        setPinging(false);

      }, 4000);

      return () => {
        clearInterval(pingInterval);
      };
    });

    return (<Box />);
  }

  const BaseProvider = (props) => {
    const {
      onFolderSelected, children
    } = props;
    const [ allFolderData, setAllFolderData ] = useState([]);
    const [ isLoading, setIsLoading ] = useState(false);
    const [ selectedFolder, setSelectedFolder ] = useState('');

    const queryAllFolder = async () => {
      setIsLoading(true);
      const result = await fetchAllFolder();
      const { data= [] } = await result.json();
      setAllFolderData(data);
      setIsLoading(false);
    };

    const handleOnFolderSelected = (folderValue, e) => {
      setSelectedFolder(folderValue);
      if (typeof onFolderSelected === 'function') onFolderSelected(folderValue);
    };

    useEffect(() => {
      queryAllFolder();
    }, []);

    return (
      <ThemeProvider theme={theme}>
        <Box sx=@{{ display: 'flex' }}>
          <CssBaseline />
          <AppBar position="fixed" sx=@{{ zIndex: (theme) => theme.zIndex.drawer + 1 }}>
          <Toolbar>
            <Typography variant="h6" noWrap component="div">
                Laravel Redis Manager
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
          <Toolbar />
           <Box sx=@{{ overflow: 'auto' }}>
            <List>
              {allFolderData.map((data, index) => (
                <ListItem key={index} disablePadding selected={selectedFolder === data}>
                  <ListItemButton onClick={(e) => handleOnFolderSelected(data, e)}>
                    <ListItemIcon>
                      <FolderIcon/>
                    </ListItemIcon>
                    <ListItemText primary={data} />
                  </ListItemButton>
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
            <Box p={2}>
            { children }
            </Box>
          </Box>
        </Box>
      </ThemeProvider>
    )
  }

  const App = () => {
    const [ folder, setFolder ] = useState(null);
    
    return (
     <BaseProvider onFolderSelected={(folderValue) => setFolder(folderValue)}>
      <Pinger/>
      <MainApp folder={folder}/>
     </BaseProvider>
    )
  }

  const root = ReactDOM.createRoot(document.getElementById("root"));
  root.render(<App />);
</script>
@endsection
